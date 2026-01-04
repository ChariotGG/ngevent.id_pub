# Arsitektur Sistem ngevent.id

## 1. High-Level Architecture

### 1.1 Layered Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           PRESENTATION LAYER                            │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────────────┐ │
│  │   Web Browser   │  │  Mobile Browser │  │   Admin Dashboard       │ │
│  │ (Blade+Alpine)  │  │     (PWA)       │  │   (Blade+Alpine)        │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                           APPLICATION LAYER                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐ │
│  │ Controllers  │─▶│   Services   │─▶│ Repositories │─▶│   Models    │ │
│  └──────────────┘  └──────────────┘  └──────────────┘  └─────────────┘ │
│         │                 │                                             │
│         ▼                 ▼                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐                  │
│  │   Requests   │  │    Jobs      │  │   Events     │                  │
│  │ (Validation) │  │   (Queue)    │  │ (Listeners)  │                  │
│  └──────────────┘  └──────────────┘  └──────────────┘                  │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          INFRASTRUCTURE LAYER                           │
│  ┌────────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐│
│  │ PostgreSQL │ │ Redis  │ │   S3   │ │ Xendit │ │  Mail  │ │Meili-  ││
│  │  Database  │ │ Cache  │ │Storage │ │Payment │ │  SMTP  │ │search  ││
│  └────────────┘ └────────┘ └────────┘ └────────┘ └────────┘ └────────┘│
└─────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Request Lifecycle

```
HTTP Request
     │
     ▼
┌─────────────────┐
│   Middleware    │──▶ Auth, CSRF, Rate Limit, Role Check
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Controller    │──▶ Receive request, delegate to service
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Form Request   │──▶ Validate input, authorize action
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Service      │──▶ Business logic, orchestration
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Repository    │──▶ Data access abstraction
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│     Model       │──▶ Eloquent ORM, database interaction
└────────┬────────┘
         │
         ▼
HTTP Response
```

## 2. Module Architecture

### 2.1 Event Module

```
app/
├── Http/
│   └── Controllers/
│       ├── Organizer/
│       │   └── EventController.php
│       └── Admin/
│           └── EventController.php
│
├── Services/
│   └── EventService.php
│       ├── create(array $data): Event
│       ├── update(Event $event, array $data): Event
│       ├── publish(Event $event): bool
│       ├── unpublish(Event $event): bool
│       ├── cancel(Event $event, string $reason): bool
│       └── duplicate(Event $event): Event
│
├── Models/
│   ├── Event.php
│   ├── EventDay.php
│   └── EventMedia.php
│
└── Enums/
    └── EventStatus.php
        ├── DRAFT
        ├── PENDING_REVIEW
        ├── APPROVED
        ├── PUBLISHED
        ├── CANCELLED
        └── COMPLETED
```

### 2.2 Ticket Module

```
app/
├── Services/
│   └── TicketService.php
│       ├── create(Event $event, array $data): Ticket
│       ├── updateStock(Ticket $ticket, int $qty): bool
│       ├── reserveStock(Ticket $ticket, int $qty): bool
│       ├── releaseStock(Ticket $ticket, int $qty): bool
│       ├── checkAvailability(Ticket $ticket, int $qty): bool
│       └── getAvailableVariants(Event $event): Collection
│
├── Models/
│   ├── Ticket.php
│   │   ├── event(): BelongsTo
│   │   ├── variants(): HasMany
│   │   └── orderItems(): HasMany
│   │
│   ├── TicketVariant.php
│   │   ├── ticket(): BelongsTo
│   │   ├── eventDay(): BelongsTo
│   │   └── isAvailable(): bool
│   │
│   └── IssuedTicket.php
│       ├── orderItem(): BelongsTo
│       ├── user(): BelongsTo
│       └── generateQRCode(): string
│
└── Enums/
    └── TicketType.php
        ├── REGULAR
        ├── VIP
        ├── BUNDLE
        ├── ADDON
        └── FREE
```

### 2.3 Order Module

```
app/
├── Services/
│   └── OrderService.php
│       ├── create(User $user, array $items, ?Voucher $voucher): Order
│       ├── calculateTotal(array $items, ?Voucher $voucher): array
│       ├── expire(Order $order): bool
│       ├── complete(Order $order): bool
│       ├── refund(Order $order, string $reason): bool
│       └── generateOrderNumber(): string
│
├── Models/
│   ├── Order.php
│   │   ├── user(): BelongsTo
│   │   ├── event(): BelongsTo
│   │   ├── items(): HasMany
│   │   ├── payment(): HasOne
│   │   ├── voucher(): BelongsTo
│   │   └── issuedTickets(): HasManyThrough
│   │
│   └── OrderItem.php
│       ├── order(): BelongsTo
│       ├── ticketVariant(): BelongsTo
│       └── issuedTicket(): HasOne
│
└── Enums/
    └── OrderStatus.php
        ├── PENDING
        ├── AWAITING_PAYMENT
        ├── PAID
        ├── COMPLETED
        ├── EXPIRED
        ├── CANCELLED
        └── REFUNDED
```

### 2.4 Payment Module

```
app/
├── Services/
│   ├── PaymentService.php
│   │   ├── createInvoice(Order $order): Payment
│   │   ├── processWebhook(array $payload): bool
│   │   ├── verifySignature(string $token): bool
│   │   ├── handlePaid(Payment $payment): bool
│   │   ├── handleExpired(Payment $payment): bool
│   │   └── requestRefund(Payment $payment): bool
│   │
│   └── Xendit/
│       ├── XenditClient.php
│       │   ├── createInvoice(array $params): array
│       │   ├── getInvoice(string $id): array
│       │   └── createRefund(string $invoiceId, int $amount): array
│       │
│       ├── InvoiceService.php
│       └── WebhookHandler.php
│
├── Models/
│   └── Payment.php
│       ├── order(): BelongsTo
│       ├── isPaid(): bool
│       ├── isExpired(): bool
│       └── getPaymentUrl(): string
│
└── Enums/
    └── PaymentStatus.php
        ├── PENDING
        ├── PAID
        ├── EXPIRED
        ├── FAILED
        └── REFUNDED
```

### 2.5 Settlement Module

```
app/
├── Services/
│   └── SettlementService.php
│       ├── createForEvent(Event $event): Settlement
│       ├── calculate(Event $event): array
│       ├── process(Settlement $settlement): bool
│       ├── markAsTransferred(Settlement $settlement): bool
│       └── getPendingSettlements(): Collection
│
├── Models/
│   └── Settlement.php
│       ├── event(): BelongsTo
│       ├── organizer(): BelongsTo
│       └── calculateNetAmount(): int
│
└── Enums/
    └── SettlementStatus.php
        ├── PENDING
        ├── PROCESSING
        ├── TRANSFERRED
        └── FAILED
```

## 3. Service Layer Pattern

### 3.1 Service Structure

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Enums\OrderStatus;
use App\Exceptions\InsufficientTicketException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private TicketService $ticketService,
        private PaymentService $paymentService,
        private VoucherService $voucherService,
    ) {}

    public function create(User $user, array $items, ?Voucher $voucher = null): Order
    {
        return DB::transaction(function () use ($user, $items, $voucher) {
            // 1. Validate and reserve ticket stock
            foreach ($items as $item) {
                $this->ticketService->reserveStock(
                    $item['ticket_variant_id'],
                    $item['quantity']
                );
            }

            // 2. Calculate totals
            $totals = $this->calculateTotal($items, $voucher);

            // 3. Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'event_id' => $items[0]['event_id'],
                'voucher_id' => $voucher?->id,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'platform_fee' => $totals['platform_fee'],
                'payment_fee' => $totals['payment_fee'],
                'total' => $totals['total'],
                'status' => OrderStatus::PENDING,
                'expires_at' => now()->addMinutes(15),
            ]);

            // 4. Create order items
            foreach ($items as $item) {
                $order->items()->create([
                    'ticket_variant_id' => $item['ticket_variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // 5. Mark voucher as used
            if ($voucher) {
                $this->voucherService->markAsUsed($voucher, $order);
            }

            // 6. Create payment invoice
            $this->paymentService->createInvoice($order);

            return $order->fresh(['items', 'payment']);
        });
    }

    public function calculateTotal(array $items, ?Voucher $voucher = null): array
    {
        $subtotal = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);
        
        $discount = 0;
        if ($voucher) {
            $discount = $this->voucherService->calculateDiscount($voucher, $subtotal);
        }

        $afterDiscount = $subtotal - $discount;
        $platformFee = (int) ($afterDiscount * config('ngevent.platform_fee_percentage') / 100);
        $paymentFee = (int) ($afterDiscount * config('ngevent.payment_fee_percentage') / 100);
        
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'platform_fee' => $platformFee,
            'payment_fee' => $paymentFee,
            'total' => $afterDiscount + $paymentFee,
        ];
    }
}
```

### 3.2 Repository Pattern

```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    public function findById(int $id): ?Event;
    public function findBySlug(string $slug): ?Event;
    public function getPublished(array $filters = []): LengthAwarePaginator;
    public function getByOrganizer(int $organizerId): LengthAwarePaginator;
    public function create(array $data): Event;
    public function update(Event $event, array $data): Event;
}
```

```php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Enums\EventStatus;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventRepository implements EventRepositoryInterface
{
    public function getPublished(array $filters = []): LengthAwarePaginator
    {
        $query = Event::query()
            ->where('status', EventStatus::PUBLISHED)
            ->where('end_date', '>=', now())
            ->with(['category', 'subcategories', 'organizer', 'media']);

        if (isset($filters['category'])) {
            $query->whereHas('category', fn($q) => 
                $q->where('slug', $filters['category'])
            );
        }

        if (isset($filters['subcategory'])) {
            $query->whereHas('subcategories', fn($q) => 
                $q->where('slug', $filters['subcategory'])
            );
        }

        if (isset($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'ilike', "%{$filters['search']}%")
                  ->orWhere('description', 'ilike', "%{$filters['search']}%");
            });
        }

        $sortBy = $filters['sort'] ?? 'start_date';
        $sortOrder = $filters['order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 12);
    }
}
```

## 4. Queue Architecture

### 4.1 Job Types

```
Jobs/
├── ProcessPayment.php      # Sync payment status
├── GenerateETicket.php     # Generate PDF ticket
├── SendOrderConfirmation.php
├── SendTicketEmail.php
├── ProcessSettlement.php
├── ExpireUnpaidOrder.php   # Auto expire after 15 min
├── NotifyOrganizerSale.php
└── SyncEventStatus.php     # Mark event as completed
```

### 4.2 Queue Configuration

```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],

// Horizon configuration for queue workers
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['high', 'default', 'low'],
            'balance' => 'auto',
            'processes' => 10,
            'tries' => 3,
        ],
    ],
],
```

### 4.3 Job Priority

| Queue | Jobs | Priority |
|-------|------|----------|
| `high` | Payment webhook, Stock update | Critical |
| `default` | Email, E-ticket generation | Normal |
| `low` | Report generation, Analytics | Low |

## 5. Caching Strategy

### 5.1 Cache Layers

```
┌─────────────────────────────────────────────────────────────┐
│                    Application Cache                         │
├─────────────────────────────────────────────────────────────┤
│  Route Cache      │  php artisan route:cache                │
│  Config Cache     │  php artisan config:cache               │
│  View Cache       │  php artisan view:cache                 │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Redis Cache                             │
├─────────────────────────────────────────────────────────────┤
│  events:published      │  10 min  │  Published event list   │
│  events:{slug}         │  5 min   │  Event detail           │
│  categories:all        │  1 hour  │  Category list          │
│  tickets:{event_id}    │  1 min   │  Available tickets      │
│  user:{id}:orders      │  5 min   │  User order history     │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database Query Cache                      │
├─────────────────────────────────────────────────────────────┤
│  Query result caching via remember()                        │
│  Automatic invalidation on model events                      │
└─────────────────────────────────────────────────────────────┘
```

### 5.2 Cache Implementation

```php
<?php

// EventService.php
public function getPublishedEvents(array $filters): LengthAwarePaginator
{
    $cacheKey = 'events:published:' . md5(serialize($filters));
    
    return Cache::tags(['events'])->remember($cacheKey, 600, function () use ($filters) {
        return $this->eventRepository->getPublished($filters);
    });
}

// Cache invalidation in Event model
protected static function booted()
{
    static::saved(function (Event $event) {
        Cache::tags(['events'])->flush();
    });

    static::deleted(function (Event $event) {
        Cache::tags(['events'])->flush();
    });
}
```

## 6. Security Architecture

### 6.1 Authentication Flow

```
┌──────────┐     ┌──────────────┐     ┌──────────────┐
│  Login   │────▶│   Sanctum    │────▶│   Session    │
│  Form    │     │  Middleware  │     │   Created    │
└──────────┘     └──────────────┘     └──────────────┘
                        │
                        ▼
┌──────────────────────────────────────────────────────────┐
│                    Role Check                             │
├──────────────────────────────────────────────────────────┤
│  Admin      │  Full access, event validation             │
│  Organizer  │  Own events, reports, settings             │
│  User       │  Browse, purchase, view tickets            │
└──────────────────────────────────────────────────────────┘
```

### 6.2 Authorization (Policies)

```php
<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Enums\UserRole;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        if ($event->isPublished()) {
            return true;
        }

        return $user->id === $event->organizer->user_id 
            || $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Event $event): bool
    {
        return $user->id === $event->organizer->user_id 
            || $user->role === UserRole::ADMIN;
    }

    public function publish(User $user, Event $event): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, Event $event): bool
    {
        if ($event->hasOrders()) {
            return false;
        }

        return $user->id === $event->organizer->user_id 
            || $user->role === UserRole::ADMIN;
    }
}
```

### 6.3 Middleware Stack

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

protected $middlewareAliases = [
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    'organizer' => \App\Http\Middleware\EnsureUserIsOrganizer::class,
    'xendit.webhook' => \App\Http\Middleware\XenditWebhookSignature::class,
    'throttle.checkout' => \App\Http\Middleware\ThrottleCheckout::class,
];
```

## 7. Error Handling

### 7.1 Custom Exceptions

```php
<?php

namespace App\Exceptions;

use Exception;

class InsufficientTicketException extends Exception
{
    public function __construct(
        public int $ticketId,
        public int $requested,
        public int $available
    ) {
        parent::__construct(
            "Insufficient stock for ticket {$ticketId}. Requested: {$requested}, Available: {$available}"
        );
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'insufficient_stock',
                'message' => 'Tiket tidak tersedia dalam jumlah yang diminta',
                'available' => $this->available,
            ], 422);
        }

        return back()->withErrors([
            'ticket' => 'Tiket tidak tersedia dalam jumlah yang diminta'
        ]);
    }
}
```

### 7.2 Global Exception Handler

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        InsufficientTicketException::class,
        VoucherInvalidException::class,
    ];

    public function render($request, Throwable $e)
    {
        if ($e instanceof PaymentFailedException) {
            return redirect()
                ->route('checkout.failed', $e->orderId)
                ->with('error', $e->getMessage());
        }

        return parent::render($request, $e);
    }
}
```

## 8. Logging & Monitoring

### 8.1 Log Channels

```php
// config/logging.php
'channels' => [
    'payment' => [
        'driver' => 'daily',
        'path' => storage_path('logs/payment.log'),
        'level' => 'debug',
        'days' => 30,
    ],
    'webhook' => [
        'driver' => 'daily',
        'path' => storage_path('logs/webhook.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    'order' => [
        'driver' => 'daily',
        'path' => storage_path('logs/order.log'),
        'level' => 'info',
        'days' => 90,
    ],
],
```

### 8.2 Activity Logging

```php
<?php

// Log payment activity
Log::channel('payment')->info('Invoice created', [
    'order_id' => $order->id,
    'invoice_id' => $payment->xendit_invoice_id,
    'amount' => $order->total,
    'user_id' => $order->user_id,
]);

// Log webhook activity
Log::channel('webhook')->info('Webhook received', [
    'type' => $payload['event'],
    'invoice_id' => $payload['id'],
    'status' => $payload['status'],
    'timestamp' => now()->toIso8601String(),
]);
```

## 9. Testing Strategy

### 9.1 Test Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegisterTest.php
│   │
│   ├── Event/
│   │   ├── BrowseEventTest.php
│   │   ├── CreateEventTest.php
│   │   └── PublishEventTest.php
│   │
│   ├── Checkout/
│   │   ├── CheckoutFlowTest.php
│   │   ├── VoucherApplicationTest.php
│   │   └── StockManagementTest.php
│   │
│   └── Payment/
│       ├── XenditWebhookTest.php
│       └── PaymentFlowTest.php
│
└── Unit/
    ├── Services/
    │   ├── OrderServiceTest.php
    │   ├── VoucherServiceTest.php
    │   └── TicketServiceTest.php
    │
    └── Models/
        ├── EventTest.php
        └── OrderTest.php
```

### 9.2 Example Test

```php
<?php

namespace Tests\Feature\Checkout;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Enums\EventStatus;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    public function test_user_can_checkout_available_tickets(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()
            ->has(Ticket::factory()->hasVariants(1, ['stock' => 10]))
            ->create(['status' => EventStatus::PUBLISHED]);

        $variant = $event->tickets->first()->variants->first();

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    [
                        'ticket_variant_id' => $variant->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_checkout_fails_when_stock_insufficient(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()
            ->has(Ticket::factory()->hasVariants(1, ['stock' => 1]))
            ->create(['status' => EventStatus::PUBLISHED]);

        $variant = $event->tickets->first()->variants->first();

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    [
                        'ticket_variant_id' => $variant->id,
                        'quantity' => 5,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('ticket');
    }
}
```

## 10. Deployment Architecture

### 10.1 Production Environment

```
┌─────────────────────────────────────────────────────────────┐
│                        Load Balancer                         │
│                     (Nginx / CloudFlare)                     │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐
│   Web Server 1   │ │   Web Server 2   │ │   Web Server 3   │
│   (PHP-FPM)      │ │   (PHP-FPM)      │ │   (PHP-FPM)      │
└──────────────────┘ └──────────────────┘ └──────────────────┘
              │               │               │
              └───────────────┼───────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  PostgreSQL  │     │    Redis     │     │  S3 Storage  │
│  (Primary)   │     │   (Cluster)  │     │              │
└──────────────┘     └──────────────┘     └──────────────┘
        │
        ▼
┌──────────────┐
│  PostgreSQL  │
│  (Replica)   │
└──────────────┘
```

### 10.2 CI/CD Pipeline

```
GitHub Push
     │
     ▼
┌─────────────────┐
│   Run Tests     │──▶ PHPUnit, PHPStan
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Build Assets  │──▶ npm run build
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Deploy        │──▶ Envoy / Deployer
└─────────────────┘
```
