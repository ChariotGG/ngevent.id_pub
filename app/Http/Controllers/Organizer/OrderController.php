<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = auth()->user()->organizer;
        $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');

        $query = Order::with(['event', 'user', 'items'])
            ->whereIn('event_id', $eventIds);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->latest()->paginate(15);

        $events = Event::where('organizer_id', $organizer->id)
            ->orderBy('title')
            ->get();

        return view('organizer.orders.index', compact('orders', 'events'));
    }

    public function show(Order $order): View
    {
        $organizer = auth()->user()->organizer;

        if ($order->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $order->load(['event', 'user', 'items.ticket', 'items.ticketVariant', 'issuedTickets']);

        return view('organizer.orders.show', compact('order'));
    }
}
