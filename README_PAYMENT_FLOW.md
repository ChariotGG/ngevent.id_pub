# Payment Flow - ngevent.id

## 1. Payment Model Overview

### 1.1 Escrow Model

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PAYMENT FLOW (ESCROW)                            │
└────────────────────────────────────────────────────────────────────────┘

     User                Platform              Xendit              Organizer
       │                    │                    │                     │
       │  1. Checkout       │                    │                     │
       │───────────────────▶│                    │                     │
       │                    │                    │                     │
       │                    │  2. Create Invoice │                     │
       │                    │───────────────────▶│                     │
       │                    │                    │                     │
       │                    │  3. Invoice URL    │                     │
       │                    │◀───────────────────│                     │
       │                    │                    │                     │
       │  4. Redirect       │                    │                     │
       │◀───────────────────│                    │                     │
       │                    │                    │                     │
       │  5. Pay            │                    │                     │
       │─────────────────────────────────────────▶                     │
       │                    │                    │                     │
       │                    │  6. Webhook (PAID) │                     │
       │                    │◀───────────────────│                     │
       │                    │                    │                     │
       │                    │  7. Update Order   │                     │
       │                    │  ┌──────────────┐  │                     │
       │                    │  │ Hold in      │  │                     │
       │                    │  │ Platform     │  │                     │
       │                    │  │ Account      │  │                     │
       │                    │  └──────────────┘  │                     │
       │                    │                    │                     │
       │  8. E-Ticket       │                    │                     │
       │◀───────────────────│                    │                     │
       │                    │                    │                     │
       │                    │                    │   9. Settlement     │
       │                    │                    │   (After Event)     │
       │                    │─────────────────────────────────────────▶│
       │                    │                    │                     │
```

### 1.2 Fee Structure

```
┌─────────────────────────────────────────────────────────────────┐
│                      FEE BREAKDOWN                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Ticket Price (subtotal)              : Rp 100.000              │
│  ─────────────────────────────────────────────────────────────  │
│  Discount (voucher)                   : - Rp 10.000             │
│  ─────────────────────────────────────────────────────────────  │
│  After Discount                       : Rp 90.000               │
│                                                                  │
│  Payment Fee (user)        : 2.5%     : + Rp 2.250              │
│  ─────────────────────────────────────────────────────────────  │
│  Total Bayar (user)                   : Rp 92.250               │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Platform Fee (organizer)  : 5%       : - Rp 4.500              │
│  ─────────────────────────────────────────────────────────────  │
│  Net to Organizer                     : Rp 85.500               │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## 2. Order & Payment States

### 2.1 Order Status Flow

```
                                    ┌─────────────┐
                                    │   PENDING   │
                                    └──────┬──────┘
                                           │
                         ┌─────────────────┼─────────────────┐
                         │                 │                 │
                         ▼                 ▼                 ▼
              ┌─────────────────┐  ┌─────────────┐   ┌─────────────┐
              │    AWAITING     │  │  CANCELLED  │   │   EXPIRED   │
              │    PAYMENT      │  │  (by user)  │   │ (15 min)    │
              └────────┬────────┘  └─────────────┘   └─────────────┘
                       │
          ┌────────────┼────────────┐
          │            │            │
          ▼            ▼            ▼
   ┌──────────┐  ┌──────────┐  ┌──────────┐
   │   PAID   │  │  EXPIRED │  │  FAILED  │
   └────┬─────┘  └──────────┘  └──────────┘
        │
        ▼
   ┌──────────┐
   │COMPLETED │◀─── Tickets issued
   └────┬─────┘
        │
        ▼
   ┌──────────┐
   │ REFUNDED │◀─── Event cancelled / Dispute
   └──────────┘
```

### 2.2 Payment Status Flow

```
┌──────────┐
│ PENDING  │──────┬──────────────────────────┐
└──────────┘      │                          │
                  │                          │
                  ▼                          ▼
           ┌──────────┐               ┌──────────┐
           │   PAID   │               │ EXPIRED  │
           └──────────┘               └──────────┘
                  │
                  ▼
           ┌──────────┐
           │ REFUNDED │
           └──────────┘
```

## 3. Xendit Integration

### 3.1 Configuration

```php
// config/xendit.php
return [
    'secret_key' => env('XENDIT_SECRET_KEY'),
    'public_key' => env('XENDIT_PUBLIC_KEY'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    
    'invoice' => [
        'expiry_duration' => 900, // 15 minutes in seconds
        'currency' => 'IDR',
        'payment_methods' => [
            'CREDIT_CARD',
            'BCA',
            'BNI',
            'BRI',
            'MANDIRI',
            'PERMATA',
            'BSI',
            'OVO',
            'DANA',
            'LINKAJA',
            'SHOPEEPAY',
            'QRIS',
        ],
    ],
    
    'callback_url' => env('APP_URL') . '/webhook/xendit',
    'success_redirect_url' => env('APP_URL') . '/checkout/success',
    'failure_redirect_url' => env('APP_URL') . '/checkout/failed',
];
```

### 3.2 Xendit Client

```php
<?php

namespace App\Services\Xendit;

use Illuminate\Support\Facades\Http;
use App\Exceptions\PaymentFailedException;

class XenditClient
{
    private string $baseUrl = 'https://api.xendit.co';
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('xendit.secret_key');
    }

    public function createInvoice(array $params): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/v2/invoices", [
                'external_id' => $params['external_id'],
                'amount' => $params['amount'],
                'payer_email' => $params['payer_email'],
                'description' => $params['description'],
                'invoice_duration' => config('xendit.invoice.expiry_duration'),
                'currency' => config('xendit.invoice.currency'),
                'payment_methods' => config('xendit.invoice.payment_methods'),
                'success_redirect_url' => config('xendit.success_redirect_url') 
                    . '?order=' . $params['external_id'],
                'failure_redirect_url' => config('xendit.failure_redirect_url') 
                    . '?order=' . $params['external_id'],
                'customer' => [
                    'given_names' => $params['customer_name'],
                    'email' => $params['payer_email'],
                    'mobile_number' => $params['customer_phone'] ?? null,
                ],
                'items' => $params['items'] ?? [],
                'fees' => $params['fees'] ?? [],
            ]);

        if (!$response->successful()) {
            throw new PaymentFailedException(
                'Failed to create invoice: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function getInvoice(string $invoiceId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

        if (!$response->successful()) {
            throw new PaymentFailedException(
                'Failed to get invoice: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function expireInvoice(string $invoiceId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/invoices/{$invoiceId}/expire!");

        return $response->json();
    }
}
```

### 3.3 Invoice Service

```php
<?php

namespace App\Services\Xendit;

use App\Models\Order;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    public function __construct(
        private XenditClient $client
    ) {}

    public function createForOrder(Order $order): Payment
    {
        $items = $order->items->map(fn($item) => [
            'name' => "{$item->ticket_name} - {$item->variant_name}",
            'quantity' => $item->quantity,
            'price' => $item->unit_price,
        ])->toArray();

        $fees = [];
        
        if ($order->payment_fee > 0) {
            $fees[] = [
                'type' => 'Payment Fee',
                'value' => $order->payment_fee,
            ];
        }

        $invoice = $this->client->createInvoice([
            'external_id' => $order->order_number,
            'amount' => $order->total,
            'payer_email' => $order->customer_email,
            'description' => "Tiket untuk {$order->event->title}",
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'items' => $items,
            'fees' => $fees,
        ]);

        Log::channel('payment')->info('Invoice created', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'invoice_id' => $invoice['id'],
            'amount' => $order->total,
        ]);

        return Payment::create([
            'order_id' => $order->id,
            'xendit_invoice_id' => $invoice['id'],
            'xendit_invoice_url' => $invoice['invoice_url'],
            'xendit_external_id' => $order->order_number,
            'amount' => $order->total,
            'status' => PaymentStatus::PENDING,
            'expires_at' => now()->addSeconds(config('xendit.invoice.expiry_duration')),
            'metadata' => [
                'created_at' => $invoice['created'],
                'expiry_date' => $invoice['expiry_date'],
            ],
        ]);
    }
}
```

### 3.4 Webhook Handler

```php
<?php

namespace App\Services\Xendit;

use App\Models\Payment;
use App\Models\Order;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Events\PaymentReceived;
use App\Jobs\GenerateETicket;
use App\Jobs\SendOrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookHandler
{
    public function handle(array $payload): bool
    {
        Log::channel('webhook')->info('Webhook received', [
            'event' => $payload['status'] ?? 'unknown',
            'invoice_id' => $payload['id'] ?? null,
            'external_id' => $payload['external_id'] ?? null,
        ]);

        // Idempotency check
        $idempotencyKey = "webhook:{$payload['id']}:{$payload['status']}";
        if (Cache::has($idempotencyKey)) {
            Log::channel('webhook')->info('Duplicate webhook ignored', [
                'invoice_id' => $payload['id'],
            ]);
            return true;
        }

        $payment = Payment::where('xendit_invoice_id', $payload['id'])->first();
        
        if (!$payment) {
            Log::channel('webhook')->warning('Payment not found', [
                'invoice_id' => $payload['id'],
            ]);
            return false;
        }

        $status = $payload['status'];

        return match ($status) {
            'PAID', 'SETTLED' => $this->handlePaid($payment, $payload),
            'EXPIRED' => $this->handleExpired($payment, $payload),
            default => $this->handleUnknown($payment, $payload),
        };
    }

    private function handlePaid(Payment $payment, array $payload): bool
    {
        // Prevent double payment
        if ($payment->status === PaymentStatus::PAID) {
            return true;
        }

        return DB::transaction(function () use ($payment, $payload) {
            // Update payment
            $payment->update([
                'status' => PaymentStatus::PAID,
                'payment_method' => $payload['payment_method'] ?? null,
                'payment_channel' => $payload['payment_channel'] ?? null,
                'paid_at' => now(),
                'webhook_payload' => $payload,
            ]);

            // Update order
            $order = $payment->order;
            $order->update([
                'status' => OrderStatus::PAID,
                'paid_at' => now(),
            ]);

            // Update ticket stock
            foreach ($order->items as $item) {
                $variant = $item->ticketVariant;
                $variant->decrement('reserved_count', $item->quantity);
                $variant->increment('sold_count', $item->quantity);
            }

            // Dispatch events & jobs
            event(new PaymentReceived($payment));
            GenerateETicket::dispatch($order);
            SendOrderConfirmation::dispatch($order);

            // Set idempotency key
            Cache::put(
                "webhook:{$payload['id']}:{$payload['status']}", 
                true, 
                now()->addDays(7)
            );

            Log::channel('payment')->info('Payment successful', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
            ]);

            return true;
        });
    }

    private function handleExpired(Payment $payment, array $payload): bool
    {
        if ($payment->status !== PaymentStatus::PENDING) {
            return true;
        }

        return DB::transaction(function () use ($payment, $payload) {
            $payment->update([
                'status' => PaymentStatus::EXPIRED,
                'webhook_payload' => $payload,
            ]);

            $order = $payment->order;
            $order->update([
                'status' => OrderStatus::EXPIRED,
            ]);

            // Release reserved stock
            foreach ($order->items as $item) {
                $item->ticketVariant->decrement('reserved_count', $item->quantity);
            }

            // Release voucher usage
            if ($order->voucher_id) {
                $order->voucherUsage?->delete();
                $order->voucher?->decrement('usage_count');
            }

            Log::channel('payment')->info('Payment expired', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ]);

            return true;
        });
    }

    private function handleUnknown(Payment $payment, array $payload): bool
    {
        Log::channel('webhook')->warning('Unknown webhook status', [
            'payment_id' => $payment->id,
            'status' => $payload['status'] ?? 'unknown',
            'payload' => $payload,
        ]);

        return true;
    }
}
```

### 3.5 Webhook Controller

```php
<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Xendit\WebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditController extends Controller
{
    public function __construct(
        private WebhookHandler $webhookHandler
    ) {}

    public function handle(Request $request)
    {
        Log::channel('webhook')->debug('Raw webhook payload', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        try {
            $result = $this->webhookHandler->handle($request->all());
            
            return response()->json([
                'status' => $result ? 'success' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::channel('webhook')->error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
```

### 3.6 Webhook Signature Middleware

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XenditWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $webhookToken = config('xendit.webhook_token');
        $callbackToken = $request->header('x-callback-token');

        if (!$callbackToken || $callbackToken !== $webhookToken) {
            return response()->json([
                'error' => 'Invalid webhook signature',
            ], 401);
        }

        return $next($request);
    }
}
```

## 4. Checkout Flow Implementation

### 4.1 Checkout Service

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\TicketVariant;
use App\Enums\OrderStatus;
use App\Exceptions\InsufficientTicketException;
use App\Services\Xendit\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private TicketService $ticketService,
        private VoucherService $voucherService,
        private InvoiceService $invoiceService,
    ) {}

    public function create(User $user, array $items, ?string $voucherCode = null): Order
    {
        return DB::transaction(function () use ($user, $items, $voucherCode) {
            // 1. Validate & reserve stock (with pessimistic lock)
            $validatedItems = [];
            $event = null;

            foreach ($items as $item) {
                $variant = TicketVariant::lockForUpdate()
                    ->findOrFail($item['ticket_variant_id']);

                if (!$variant->isAvailable()) {
                    throw new InsufficientTicketException(
                        $variant->id,
                        $item['quantity'],
                        0
                    );
                }

                if ($variant->available_stock < $item['quantity']) {
                    throw new InsufficientTicketException(
                        $variant->id,
                        $item['quantity'],
                        $variant->available_stock
                    );
                }

                // Reserve stock
                $variant->increment('reserved_count', $item['quantity']);

                $validatedItems[] = [
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'unit_price' => $variant->price,
                    'subtotal' => $variant->price * $item['quantity'],
                ];

                $event = $variant->ticket->event;
            }

            // 2. Validate & apply voucher
            $voucher = null;
            $discount = 0;

            if ($voucherCode) {
                $voucher = $this->voucherService->validate($voucherCode, $user, $event);
                $subtotal = collect($validatedItems)->sum('subtotal');
                $discount = $this->voucherService->calculateDiscount($voucher, $subtotal);
            }

            // 3. Calculate totals
            $subtotal = collect($validatedItems)->sum('subtotal');
            $afterDiscount = $subtotal - $discount;
            $platformFee = (int) ($afterDiscount * config('ngevent.platform_fee_percentage') / 100);
            $paymentFee = (int) ($afterDiscount * config('ngevent.payment_fee_percentage') / 100);
            $total = $afterDiscount + $paymentFee;

            // 4. Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'event_id' => $event->id,
                'voucher_id' => $voucher?->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'platform_fee' => $platformFee,
                'payment_fee' => $paymentFee,
                'total' => $total,
                'status' => OrderStatus::PENDING,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'expires_at' => now()->addMinutes(15),
            ]);

            // 5. Create order items
            foreach ($validatedItems as $item) {
                $order->items()->create([
                    'ticket_variant_id' => $item['variant']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'ticket_name' => $item['variant']->ticket->name,
                    'variant_name' => $item['variant']->name,
                ]);
            }

            // 6. Record voucher usage
            if ($voucher) {
                $this->voucherService->recordUsage($voucher, $user, $order, $discount);
            }

            // 7. Create payment invoice
            $payment = $this->invoiceService->createForOrder($order);
            
            $order->update(['status' => OrderStatus::AWAITING_PAYMENT]);

            return $order->fresh(['items', 'payment', 'event']);
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'NGE';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(6));
        
        return "{$prefix}-{$date}-{$random}";
    }
}
```

### 4.2 Checkout Controller

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckoutRequest;
use App\Services\OrderService;
use App\Exceptions\InsufficientTicketException;
use App\Exceptions\VoucherInvalidException;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(CheckoutRequest $request)
    {
        try {
            $order = $this->orderService->create(
                $request->user(),
                $request->validated('items'),
                $request->validated('voucher_code')
            );

            return redirect()->route('checkout.payment', $order);

        } catch (InsufficientTicketException $e) {
            return back()->withErrors([
                'ticket' => 'Tiket tidak tersedia dalam jumlah yang diminta. Tersedia: ' . $e->available,
            ]);

        } catch (VoucherInvalidException $e) {
            return back()->withErrors([
                'voucher' => $e->getMessage(),
            ]);
        }
    }

    public function payment(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->isExpired()) {
            return redirect()->route('checkout.expired', $order);
        }

        if ($order->isPaid()) {
            return redirect()->route('checkout.success', $order);
        }

        return view('pages.checkout.payment', [
            'order' => $order->load(['items', 'payment', 'event']),
        ]);
    }

    public function success(Order $order)
    {
        $this->authorize('view', $order);

        if (!$order->isPaid()) {
            return redirect()->route('checkout.payment', $order);
        }

        return view('pages.checkout.success', [
            'order' => $order->load(['items', 'issuedTickets', 'event']),
        ]);
    }

    public function failed(Order $order)
    {
        $this->authorize('view', $order);

        return view('pages.checkout.failed', [
            'order' => $order,
            'canRetry' => $order->isExpired(),
        ]);
    }
}
```

## 5. Settlement Flow

### 5.1 Settlement Service

```php
<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Settlement;
use App\Enums\OrderStatus;
use App\Enums\SettlementStatus;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    public function createForEvent(Event $event): Settlement
    {
        $stats = $this->calculateEventStats($event);
        $organizer = $event->organizer;

        return Settlement::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'gross_amount' => $stats['gross'],
            'platform_fee' => $stats['platform_fee'],
            'payment_fee_total' => $stats['payment_fee'],
            'refund_amount' => $stats['refund'],
            'net_amount' => $stats['net'],
            'bank_name' => $organizer->bank_name,
            'bank_account_number' => $organizer->bank_account_number,
            'bank_account_name' => $organizer->bank_account_name,
            'status' => SettlementStatus::PENDING,
            'calculated_at' => now(),
        ]);
    }

    public function calculateEventStats(Event $event): array
    {
        $orders = $event->orders()
            ->whereIn('status', [OrderStatus::PAID, OrderStatus::COMPLETED])
            ->get();

        $gross = $orders->sum('subtotal');
        $discount = $orders->sum('discount');
        $platformFee = $orders->sum('platform_fee');
        $paymentFee = $orders->sum('payment_fee');

        $refundedOrders = $event->orders()
            ->where('status', OrderStatus::REFUNDED)
            ->get();
        $refund = $refundedOrders->sum('subtotal');

        $net = $gross - $discount - $platformFee - $refund;

        return [
            'gross' => $gross,
            'discount' => $discount,
            'platform_fee' => $platformFee,
            'payment_fee' => $paymentFee,
            'refund' => $refund,
            'net' => max(0, $net),
            'order_count' => $orders->count(),
            'ticket_sold' => $orders->sum(fn($o) => $o->items->sum('quantity')),
        ];
    }

    public function process(Settlement $settlement): bool
    {
        if ($settlement->status !== SettlementStatus::PENDING) {
            return false;
        }

        $settlement->update([
            'status' => SettlementStatus::PROCESSING,
        ]);

        // TODO: Integrate with bank transfer API or manual process

        return true;
    }

    public function markAsTransferred(
        Settlement $settlement,
        string $reference,
        ?string $proofPath = null
    ): bool {
        return $settlement->update([
            'status' => SettlementStatus::TRANSFERRED,
            'transfer_reference' => $reference,
            'transfer_proof' => $proofPath,
            'transferred_at' => now(),
        ]);
    }
}
```

### 5.2 Settlement Scheduler

```php
<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Enums\EventStatus;
use App\Services\SettlementService;
use Illuminate\Console\Command;

class ProcessSettlements extends Command
{
    protected $signature = 'settlements:process';
    protected $description = 'Create settlements for completed events';

    public function handle(SettlementService $settlementService)
    {
        // Events yang sudah selesai dan belum ada settlement
        $events = Event::query()
            ->where('status', EventStatus::COMPLETED)
            ->whereDoesntHave('settlement')
            ->where('end_date', '<=', now()->subDays(config('ngevent.settlement_delay_days')))
            ->get();

        foreach ($events as $event) {
            $this->info("Processing settlement for: {$event->title}");
            $settlementService->createForEvent($event);
        }

        $this->info("Processed {$events->count()} settlements");
    }
}

// Scheduler in Console/Kernel.php
$schedule->command('settlements:process')->dailyAt('06:00');
```

## 6. Edge Cases & Error Handling

### 6.1 Double Payment Prevention

```php
// Di WebhookHandler::handlePaid()
if ($payment->status === PaymentStatus::PAID) {
    Log::channel('payment')->info('Duplicate payment ignored', [
        'payment_id' => $payment->id,
    ]);
    return true;
}

// Idempotency key
$idempotencyKey = "webhook:{$payload['id']}:{$payload['status']}";
if (Cache::has($idempotencyKey)) {
    return true;
}
```

### 6.2 Stock Exhausted During Checkout

```php
// Pessimistic locking
$variant = TicketVariant::lockForUpdate()->findOrFail($id);

if ($variant->available_stock < $quantity) {
    throw new InsufficientTicketException($variant->id, $quantity, $variant->available_stock);
}
```

### 6.3 Invoice Expiration

```php
<?php

namespace App\Jobs;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpireUnpaidOrder implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function handle()
    {
        if ($this->order->status !== OrderStatus::AWAITING_PAYMENT) {
            return;
        }

        if (!$this->order->isExpired()) {
            return;
        }

        DB::transaction(function () {
            $this->order->update([
                'status' => OrderStatus::EXPIRED,
            ]);

            $this->order->payment?->update([
                'status' => PaymentStatus::EXPIRED,
            ]);

            // Release stock
            foreach ($this->order->items as $item) {
                $item->ticketVariant->decrement('reserved_count', $item->quantity);
            }

            // Release voucher
            if ($this->order->voucher_id) {
                $this->order->voucherUsage?->delete();
                $this->order->voucher?->decrement('usage_count');
            }
        });
    }
}

// Dispatch with delay
ExpireUnpaidOrder::dispatch($order)->delay(now()->addMinutes(16));
```

### 6.4 Refund Process

```php
<?php

namespace App\Services;

class RefundService
{
    public function processRefund(Order $order, string $reason): bool
    {
        if (!$order->isPaid()) {
            return false;
        }

        return DB::transaction(function () use ($order, $reason) {
            // Update order
            $order->update([
                'status' => OrderStatus::REFUNDED,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            // Update payment
            $order->payment->update([
                'status' => PaymentStatus::REFUNDED,
            ]);

            // Invalidate tickets
            $order->issuedTickets()->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

            // Return stock
            foreach ($order->items as $item) {
                $item->ticketVariant->decrement('sold_count', $item->quantity);
            }

            // TODO: Process actual refund via Xendit
            // $this->xenditClient->createRefund(...)

            Log::channel('payment')->info('Refund processed', [
                'order_id' => $order->id,
                'amount' => $order->total,
                'reason' => $reason,
            ]);

            return true;
        });
    }
}
```

### 6.5 Event Cancellation

```php
<?php

namespace App\Services;

class EventService
{
    public function cancel(Event $event, string $reason): bool
    {
        return DB::transaction(function () use ($event, $reason) {
            $event->update([
                'status' => EventStatus::CANCELLED,
            ]);

            // Refund all paid orders
            $paidOrders = $event->orders()
                ->whereIn('status', [OrderStatus::PAID, OrderStatus::COMPLETED])
                ->get();

            foreach ($paidOrders as $order) {
                $this->refundService->processRefund(
                    $order,
                    "Event dibatalkan: {$reason}"
                );
            }

            // Notify all ticket holders
            // SendEventCancellationNotification::dispatch($event);

            return true;
        });
    }
}
```

## 7. Monitoring & Alerts

### 7.1 Health Checks

```php
// routes/api.php
Route::get('/health/payment', function () {
    // Check Xendit connectivity
    try {
        $client = app(XenditClient::class);
        // Simple API call to verify
        return response()->json(['status' => 'healthy']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'unhealthy', 'error' => $e->getMessage()], 500);
    }
});
```

### 7.2 Failed Payment Alerts

```php
// Event listener
class NotifyFailedPayments
{
    public function handle(PaymentFailed $event)
    {
        // Notify admin via Slack/Email
        Log::channel('slack')->critical('Payment failed', [
            'order_id' => $event->payment->order_id,
            'amount' => $event->payment->amount,
            'error' => $event->error,
        ]);
    }
}
```
