<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Events\OrderExpired;
use App\Events\OrderRefunded;
use App\Exceptions\InsufficientTicketException;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketVariant;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private TicketService $ticketService,
        private VoucherService $voucherService,
        private PaymentService $paymentService,
    ) {}

    public function create(User $user, Event $event, array $items, array $customerData, ?Voucher $voucher = null): Order
    {
        return DB::transaction(function () use ($user, $event, $items, $customerData, $voucher) {
            // Reserve stock for all items
            foreach ($items as $item) {
                $this->ticketService->reserveStock($item['ticket_variant_id'], $item['quantity']);
            }

            // Calculate totals
            $totals = $this->calculateTotals($items, $voucher);

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'event_id' => $event->id,
                'voucher_id' => $voucher?->id,
                'customer_name' => $customerData['name'],
                'customer_email' => $customerData['email'],
                'customer_phone' => $customerData['phone'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'platform_fee' => $totals['platform_fee'],
                'payment_fee' => $totals['payment_fee'],
                'total' => $totals['total'],
                'status' => OrderStatus::PENDING,
                'expires_at' => now()->addMinutes(config('ngevent.order_expiry_minutes', 15)),
            ]);

            // Create order items
            foreach ($items as $item) {
                $variant = TicketVariant::with('ticket')->find($item['ticket_variant_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_id' => $variant->ticket_id,
                    'ticket_variant_id' => $variant->id,
                    'ticket_name' => $variant->ticket->name,
                    'variant_name' => $variant->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $variant->price,
                    'subtotal' => $variant->price * $item['quantity'],
                ]);
            }

            // Record voucher usage
            if ($voucher) {
                $this->voucherService->recordUsage($voucher, $user, $order, $totals['discount']);
            }

            // Create payment invoice
            $payment = $this->paymentService->createInvoice($order);

            // Update order status
            $order->update(['status' => OrderStatus::AWAITING_PAYMENT]);

            event(new OrderCreated($order));

            return $order->fresh(['items', 'payment']);
        });
    }

    public function calculateTotals(array $items, ?Voucher $voucher = null): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $variant = TicketVariant::find($item['ticket_variant_id']);
            $subtotal += $variant->price * $item['quantity'];
        }

        $discount = 0;
        if ($voucher) {
            $discount = $voucher->calculateDiscount($subtotal);
        }

        $afterDiscount = $subtotal - $discount;

        $platformFeeRate = config('ngevent.platform_fee_percentage', 5) / 100;
        $paymentFeeRate = config('ngevent.payment_fee_percentage', 2.5) / 100;

        $platformFee = (int) ceil($afterDiscount * $platformFeeRate);
        $paymentFee = (int) ceil($afterDiscount * $paymentFeeRate);

        $total = $afterDiscount + $paymentFee;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'platform_fee' => $platformFee,
            'payment_fee' => $paymentFee,
            'total' => $total,
            'net_amount' => $afterDiscount - $platformFee, // Amount organizer receives
        ];
    }

    public function markAsPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => OrderStatus::PAID,
                'paid_at' => now(),
            ]);

            // Confirm stock sales
            foreach ($order->items as $item) {
                $this->ticketService->confirmSale($item->ticket_variant_id, $item->quantity);
            }

            event(new OrderPaid($order));

            return $order->fresh();
        });
    }

    public function expire(Order $order): Order
    {
        if ($order->isPaid()) {
            return $order;
        }

        return DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::EXPIRED]);

            // Release reserved stock
            foreach ($order->items as $item) {
                $this->ticketService->releaseStock($item->ticket_variant_id, $item->quantity);
            }

            // Release voucher usage
            if ($order->voucher_id) {
                $this->voucherService->releaseUsage($order);
            }

            event(new OrderExpired($order));

            return $order->fresh();
        });
    }

    public function cancel(Order $order, string $reason): Order
    {
        if (!$order->canCancel()) {
            return $order;
        }

        return DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => OrderStatus::CANCELLED,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Release reserved stock
            foreach ($order->items as $item) {
                $this->ticketService->releaseStock($item->ticket_variant_id, $item->quantity);
            }

            // Release voucher usage
            if ($order->voucher_id) {
                $this->voucherService->releaseUsage($order);
            }

            return $order->fresh();
        });
    }

    public function refund(Order $order, string $reason): Order
    {
        if (!$order->canRefund()) {
            return $order;
        }

        return DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => OrderStatus::REFUNDED,
                'cancellation_reason' => $reason,
            ]);

            // Return sold stock
            foreach ($order->items as $item) {
                $this->ticketService->returnStock($item->ticket_variant_id, $item->quantity);
            }

            // Invalidate issued tickets
            $order->issuedTickets()->update(['is_used' => true, 'check_in_notes' => 'Order refunded']);

            // Update payment status
            $order->payment?->update(['status' => PaymentStatus::REFUNDED]);

            event(new OrderRefunded($order));

            return $order->fresh();
        });
    }

    public function complete(Order $order): Order
    {
        if ($order->status !== OrderStatus::PAID) {
            return $order;
        }

        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => OrderStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            return $order->fresh();
        });
    }

    public function getUserOrders(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Order::query()
            ->where('user_id', $user->id)
            ->with(['event', 'items', 'payment'])
            ->latest()
            ->paginate($perPage);
    }

    public function getEventOrders(Event $event, array $filters = []): LengthAwarePaginator
    {
        $query = Order::query()
            ->where('event_id', $event->id)
            ->with(['user', 'items']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                    ->orWhere('customer_name', 'ilike', "%{$search}%")
                    ->orWhere('customer_email', 'ilike', "%{$search}%");
            });
        }

        return $query->latest()->paginate(20);
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)
            ->with(['event', 'items.ticket', 'items.ticketVariant', 'payment', 'issuedTickets'])
            ->first();
    }

    public function getExpiredOrders(): Collection
    {
        return Order::query()
            ->whereIn('status', [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT])
            ->where('expires_at', '<', now())
            ->get();
    }

    protected function generateOrderNumber(): string
    {
        $prefix = 'NGE';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        $orderNumber = "{$prefix}{$date}{$random}";

        while (Order::where('order_number', $orderNumber)->exists()) {
            $random = strtoupper(Str::random(6));
            $orderNumber = "{$prefix}{$date}{$random}";
        }

        return $orderNumber;
    }
}
