<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $order->load(['event', 'items.ticket', 'payment', 'issuedTickets']);

        return view('pages.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        // TODO: Implement cancel logic
        return back()->with('success', 'Order dibatalkan');
    }
}
