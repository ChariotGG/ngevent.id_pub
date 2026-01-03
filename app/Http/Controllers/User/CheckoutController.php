<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Event $event): View
    {
        if (!$event->isPublished()) {
            abort(404);
        }

        if ($event->isPast()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Event sudah berakhir');
        }

        $event->load(['tickets.variants' => function ($query) {
            $query->where('is_active', true);
        }, 'days']);

        return view('pages.checkout.index', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'tickets' => 'required|array|min:1',
            'tickets.*.variant_id' => 'required|exists:ticket_variants,id',
            'tickets.*.quantity' => 'required|integer|min:0|max:10',
            'voucher_code' => 'nullable|string|max:50',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_phone' => 'required|string|max:20',
        ]);

        // Filter tickets with quantity > 0
        $selectedTickets = collect($request->tickets)->filter(fn($t) => $t['quantity'] > 0);

        if ($selectedTickets->isEmpty()) {
            return back()->with('error', 'Pilih minimal 1 tiket');
        }

        try {
            DB::beginTransaction();

            // Check availability and reserve stock
            $orderItems = [];
            $subtotal = 0;

            foreach ($selectedTickets as $ticketData) {
                $variant = TicketVariant::with('ticket')->findOrFail($ticketData['variant_id']);

                // Check if variant belongs to this event
                if ($variant->ticket->event_id !== $event->id) {
                    throw new \Exception('Tiket tidak valid untuk event ini');
                }

                // Check availability
                $available = $variant->stock - $variant->sold_count - $variant->reserved_count;
                if ($ticketData['quantity'] > $available) {
                    throw new \Exception("Tiket {$variant->ticket->name} tidak cukup tersedia");
                }

                // Reserve stock
                $variant->increment('reserved_count', $ticketData['quantity']);

                $itemSubtotal = $variant->price * $ticketData['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'variant' => $variant,
                    'quantity' => $ticketData['quantity'],
                    'price' => $variant->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate fees
            $platformFee = 0;
            $paymentFee = (int) ceil($subtotal * 0.025);
            $discount = 0;
            $voucherId = null;

            $total = $subtotal + $paymentFee - $discount;

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'voucher_id' => $voucherId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'platform_fee' => $platformFee,
                'payment_fee' => $paymentFee,
                'total' => $total,
                'attendee_name' => $request->attendee_name,
                'attendee_email' => $request->attendee_email,
                'attendee_phone' => $request->attendee_phone,
                'expires_at' => now()->addMinutes(15),
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_id' => $item['variant']->ticket_id,
                    'ticket_variant_id' => $item['variant']->id,
                    'ticket_name' => $item['variant']->ticket->name,
                    'variant_name' => $item['variant']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            return redirect()->route('checkout.payment', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function payment(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order);
        }

        if ($order->isExpired()) {
            return redirect()->route('checkout.expired', $order);
        }

        $order->load(['event', 'items.ticket', 'items.ticketVariant']);

        return view('pages.checkout.payment', compact('order'));
    }

    public function processPayment(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order);
        }

        try {
            DB::beginTransaction();

            // Update order status
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Convert reserved to sold
            foreach ($order->items as $item) {
                $item->ticketVariant->decrement('reserved_count', $item->quantity);
                $item->ticketVariant->increment('sold_count', $item->quantity);
            }

            // Generate issued tickets
            $this->generateIssuedTickets($order);

            DB::commit();

            return redirect()->route('checkout.success', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.failed', $order)
                ->with('error', $e->getMessage());
        }
    }

    public function success(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['event', 'items', 'issuedTickets']);

        return view('pages.checkout.success', compact('order'));
    }

    public function failed(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['event']);

        return view('pages.checkout.failed', compact('order'));
    }

    public function expired(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Release reserved tickets if not already done
        if ($order->status === 'pending') {
            $this->releaseReservedTickets($order);
            $order->update(['status' => 'expired']);
        }

        $order->load(['event']);

        return view('pages.checkout.expired', compact('order'));
    }

    protected function generateOrderNumber(): string
    {
        $prefix = 'NGE';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return "{$prefix}{$date}{$random}";
    }

    protected function generateIssuedTickets(Order $order): void
    {
        foreach ($order->items as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                $order->issuedTickets()->create([
                    'user_id' => $order->user_id,
                    'order_item_id' => $item->id,
                    'code' => $this->generateTicketCode(),
                    'attendee_name' => $order->attendee_name,
                    'attendee_email' => $order->attendee_email,
                    'attendee_phone' => $order->attendee_phone,
                    'status' => 'active',
                ]);
            }
        }
    }

    protected function generateTicketCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));
        } while (\App\Models\IssuedTicket::where('code', $code)->exists());

        return $code;
    }

    protected function releaseReservedTickets(Order $order): void
    {
        foreach ($order->items as $item) {
            $item->ticketVariant->decrement('reserved_count', $item->quantity);
        }
    }
}
