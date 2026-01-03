<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['event', 'items'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pages.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['event', 'items.ticket', 'items.ticketVariant', 'issuedTickets.orderItem']);

        return view('pages.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Cek status - convert enum ke string jika perlu
        $status = $order->status instanceof OrderStatus ? $order->status->value : $order->status;

        if ($status !== 'pending') {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan');
        }

        // Release reserved tickets
        foreach ($order->items as $item) {
            if ($item->ticketVariant) {
                $item->ticketVariant->decrement('reserved_count', $item->quantity);
            }
        }

        $order->update([
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Dibatalkan oleh user',
        ]);

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan');
    }
}
