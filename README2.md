# 🎫 ngevent.id - Event Ticketing Platform

> Platform ticketing modern untuk aktivitas dan event di Indonesia

## 📋 Daftar Isi

1. [Overview](#-overview)
2. [Tech Stack](#-tech-stack)
3. [Arsitektur Sistem](#-arsitektur-sistem)
4. [Struktur Folder](#-struktur-folder)
5. [Database Schema](#-database-schema)
6. [Payment Flow](#-payment-flow)
7. [UX Flow](#-ux-flow)
8. [Best Practices](#-best-practices)
9. [Getting Started](#-getting-started)

---

## 🎯 Overview

**ngevent.id** adalah platform ticketing yang berfokus pada:

| Kategori | Deskripsi |
|----------|-----------|
| 🎭 Cosplay & Pop Culture | Anime expo, comic con, cosplay competition |
| 🎵 Music & Concert | Live band, DJ, orchestra, festival |
| ⚽ Sports | Marathon, tournament, championship |

### Fitur Utama

- ✅ Multi-day event support
- ✅ Multiple ticket types (Regular, VIP, Bundle)
- ✅ Add-on tickets (Meet & Greet, Merchandise)
- ✅ Free event with registration
- ✅ Voucher system
- ✅ Escrow payment model
- ✅ Mobile-first responsive design

---

## 🛠 Tech Stack

```
Backend:     Laravel 11.x (PHP 8.2+)
Frontend:    Blade + Alpine.js + Tailwind CSS
Database:    PostgreSQL 15+
Cache:       Redis
Queue:       Laravel Horizon + Redis
Payment:     Xendit
Storage:     S3-compatible (MinIO/AWS)
Search:      Laravel Scout + Meilisearch
```

---

## 🏗 Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│  Web Browser (Blade + Alpine.js + Tailwind CSS)                 │
│  Mobile Browser (PWA-ready)                                      │
└───────────────────────────────┬─────────────────────────────────┘
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│                        APPLICATION LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│  Controllers → Form Requests → Services → Repositories          │
│  Policies (Authorization) │ Events & Listeners │ Jobs (Queue)   │
└───────────────────────────────┬─────────────────────────────────┘
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│                        DOMAIN LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│  Models │ Enums │ Value Objects │ Domain Events                 │
└───────────────────────────────┬─────────────────────────────────┘
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│                        INFRASTRUCTURE LAYER                      │
├─────────────────────────────────────────────────────────────────┤
│  PostgreSQL │ Redis │ S3 │ Xendit │ Meilisearch │ SMTP          │
└─────────────────────────────────────────────────────────────────┘
```

### Service-Oriented Architecture

```
app/Services/
├── EventService.php          # Event CRUD, validation, publishing
├── TicketService.php         # Ticket management, stock control
├── OrderService.php          # Order creation, status management
├── PaymentService.php        # Xendit integration, webhook handling
├── VoucherService.php        # Voucher validation, usage tracking
├── SettlementService.php     # Fund distribution, organizer payout
└── NotificationService.php   # Email, SMS, push notifications
```

---

## 📁 Struktur Folder

```
ngevent/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── ExpireInvoices.php
│   │       ├── ProcessSettlements.php
│   │       └── SyncEventStatus.php
│   │
│   ├── Enums/
│   │   ├── EventStatus.php
│   │   ├── OrderStatus.php
│   │   ├── PaymentStatus.php
│   │   ├── SettlementStatus.php
│   │   ├── TicketType.php
│   │   ├── UserRole.php
│   │   └── VoucherType.php
│   │
│   ├── Events/
│   │   ├── OrderCreated.php
│   │   ├── PaymentReceived.php
│   │   ├── TicketIssued.php
│   │   └── SettlementProcessed.php
│   │
│   ├── Exceptions/
│   │   ├── InsufficientTicketException.php
│   │   ├── PaymentFailedException.php
│   │   ├── VoucherInvalidException.php
│   │   └── Handler.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EventController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── SettlementController.php
│   │   │   │   ├── VoucherController.php
│   │   │   │   └── UserController.php
│   │   │   │
│   │   │   ├── Organizer/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EventController.php
│   │   │   │   ├── TicketController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── ProfileController.php
│   │   │   │
│   │   │   ├── User/
│   │   │   │   ├── EventController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── TicketController.php
│   │   │   │   └── ProfileController.php
│   │   │   │
│   │   │   ├── Webhook/
│   │   │   │   └── XenditController.php
│   │   │   │
│   │   │   └── Auth/
│   │   │       ├── LoginController.php
│   │   │       ├── RegisterController.php
│   │   │       └── OAuthController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── EnsureUserIsAdmin.php
│   │   │   ├── EnsureUserIsOrganizer.php
│   │   │   ├── XenditWebhookSignature.php
│   │   │   └── ThrottleCheckout.php
│   │   │
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreVoucherRequest.php
│   │       │   └── UpdateEventStatusRequest.php
│   │       │
│   │       ├── Organizer/
│   │       │   ├── StoreEventRequest.php
│   │       │   ├── UpdateEventRequest.php
│   │       │   └── StoreTicketRequest.php
│   │       │
│   │       └── User/
│   │           ├── CheckoutRequest.php
│   │           └── ApplyVoucherRequest.php
│   │
│   ├── Jobs/
│   │   ├── ProcessPayment.php
│   │   ├── GenerateETicket.php
│   │   ├── SendOrderConfirmation.php
│   │   ├── ProcessSettlement.php
│   │   └── ExpireUnpaidOrder.php
│   │
│   ├── Listeners/
│   │   ├── SendTicketEmail.php
│   │   ├── UpdateTicketStock.php
│   │   ├── LogPaymentActivity.php
│   │   └── NotifyOrganizerSale.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Event.php
│   │   ├── EventDay.php
│   │   ├── Category.php
│   │   ├── Subcategory.php
│   │   ├── Ticket.php
│   │   ├── TicketVariant.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   ├── Voucher.php
│   │   ├── VoucherUsage.php
│   │   ├── Settlement.php
│   │   ├── Organizer.php
│   │   ├── OrganizerSocialLink.php
│   │   └── IssuedTicket.php
│   │
│   ├── Policies/
│   │   ├── EventPolicy.php
│   │   ├── OrderPolicy.php
│   │   ├── TicketPolicy.php
│   │   └── SettlementPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   └── EventServiceProvider.php
│   │
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── EventRepositoryInterface.php
│   │   │   ├── OrderRepositoryInterface.php
│   │   │   └── TicketRepositoryInterface.php
│   │   │
│   │   └── Eloquent/
│   │       ├── EventRepository.php
│   │       ├── OrderRepository.php
│   │       └── TicketRepository.php
│   │
│   └── Services/
│       ├── EventService.php
│       ├── TicketService.php
│       ├── OrderService.php
│       ├── PaymentService.php
│       ├── VoucherService.php
│       ├── SettlementService.php
│       ├── ETicketService.php
│       └── Xendit/
│           ├── XenditClient.php
│           ├── InvoiceService.php
│           └── WebhookHandler.php
│
├── bootstrap/
│   └── app.php
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── queue.php
│   ├── xendit.php
│   ├── ngevent.php          # Platform-specific config
│   └── services.php
│
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── EventFactory.php
│   │   └── TicketFactory.php
│   │
│   ├── migrations/
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_organizers_table.php
│   │   ├── 0003_create_categories_table.php
│   │   ├── 0004_create_subcategories_table.php
│   │   ├── 0005_create_events_table.php
│   │   ├── 0006_create_event_days_table.php
│   │   ├── 0007_create_event_category_table.php
│   │   ├── 0008_create_event_subcategory_table.php
│   │   ├── 0009_create_tickets_table.php
│   │   ├── 0010_create_ticket_variants_table.php
│   │   ├── 0011_create_orders_table.php
│   │   ├── 0012_create_order_items_table.php
│   │   ├── 0013_create_payments_table.php
│   │   ├── 0014_create_vouchers_table.php
│   │   ├── 0015_create_voucher_usages_table.php
│   │   ├── 0016_create_settlements_table.php
│   │   ├── 0017_create_issued_tickets_table.php
│   │   └── 0018_create_organizer_social_links_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php
│       ├── SubcategorySeeder.php
│       └── AdminSeeder.php
│
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── images/
│
├── resources/
│   ├── css/
│   │   └── app.css
│   │
│   ├── js/
│   │   ├── app.js
│   │   └── components/
│   │       ├── ticket-selector.js
│   │       ├── checkout-timer.js
│   │       └── seat-picker.js
│   │
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── admin.blade.php
│       │   └── organizer.blade.php
│       │
│       ├── components/
│       │   ├── event-card.blade.php
│       │   ├── ticket-card.blade.php
│       │   ├── category-badge.blade.php
│       │   ├── countdown-timer.blade.php
│       │   └── price-display.blade.php
│       │
│       ├── pages/
│       │   ├── home.blade.php
│       │   ├── events/
│       │   │   ├── index.blade.php
│       │   │   ├── show.blade.php
│       │   │   └── partials/
│       │   │       ├── _info.blade.php
│       │   │       ├── _tickets.blade.php
│       │   │       ├── _schedule.blade.php
│       │   │       └── _organizer.blade.php
│       │   │
│       │   ├── checkout/
│       │   │   ├── index.blade.php
│       │   │   ├── payment.blade.php
│       │   │   ├── success.blade.php
│       │   │   └── failed.blade.php
│       │   │
│       │   └── tickets/
│       │       ├── index.blade.php
│       │       └── show.blade.php
│       │
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── events/
│       │   ├── categories/
│       │   ├── settlements/
│       │   └── vouchers/
│       │
│       ├── organizer/
│       │   ├── dashboard.blade.php
│       │   ├── events/
│       │   ├── tickets/
│       │   └── reports/
│       │
│       └── emails/
│           ├── order-confirmation.blade.php
│           ├── ticket-issued.blade.php
│           └── settlement-processed.blade.php
│
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── admin.php
│   ├── organizer.php
│   └── webhook.php
│
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── CheckoutTest.php
│   │   ├── PaymentWebhookTest.php
│   │   └── EventCreationTest.php
│   │
│   └── Unit/
│       ├── VoucherServiceTest.php
│       ├── TicketServiceTest.php
│       └── PriceCalculationTest.php
│
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── phpunit.xml
```

---

## 💾 Database Schema

### Entity Relationship Diagram

```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    users     │───────│  organizers  │───────│    events    │
└──────────────┘       └──────────────┘       └──────────────┘
       │                      │                      │
       │                      │               ┌──────┴──────┐
       │                      │               │             │
       │               ┌──────────────┐  ┌────────┐  ┌────────────┐
       │               │   social     │  │ event  │  │   tickets  │
       │               │   links      │  │ days   │  └────────────┘
       │               └──────────────┘  └────────┘        │
       │                                                    │
       │                                             ┌──────────────┐
       │                                             │   ticket     │
       │                                             │   variants   │
       │                                             └──────────────┘
       │                                                    │
┌──────────────┐                                           │
│    orders    │───────────────────────────────────────────┘
└──────────────┘
       │
       ├──────────────┐
       │              │
┌──────────────┐ ┌──────────────┐
│ order_items  │ │   payments   │
└──────────────┘ └──────────────┘
       │
┌──────────────┐
│   issued     │
│   tickets    │
└──────────────┘
```

### Tabel Detail

Lihat file `DATABASE.md` untuk schema lengkap.

---

## 💳 Payment Flow

### Alur Pembayaran (Escrow Model)

```
┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐
│  User   │────▶│ Platform│────▶│ Xendit  │────▶│ Platform│────▶│Organizer│
│ Bayar   │     │ Create  │     │ Invoice │     │ Rekening│     │ Setelah │
│         │     │ Invoice │     │         │     │ Escrow  │     │ Event   │
└─────────┘     └─────────┘     └─────────┘     └─────────┘     └─────────┘
```

### Status Flow

```
Order Created
     │
     ▼
┌─────────────┐
│   PENDING   │◀────────────────────────────────────────┐
└─────────────┘                                         │
     │                                                  │
     ▼                                                  │
┌─────────────┐     ┌─────────────┐     ┌─────────────┐│
│  AWAITING   │────▶│    PAID     │────▶│  COMPLETED  ││
│  PAYMENT    │     └─────────────┘     └─────────────┘│
└─────────────┘            │                           │
     │                     ▼                           │
     │              ┌─────────────┐                    │
     │              │   REFUNDED  │                    │
     │              └─────────────┘                    │
     ▼                                                 │
┌─────────────┐                                        │
│   EXPIRED   │────────────────────────────────────────┘
└─────────────┘         (Retry Available)
```

Lihat file `PAYMENT_FLOW.md` untuk detail lengkap.

---

## 🎨 UX Flow

### User Journey - Beli Tiket

```
Homepage ──▶ Browse Event ──▶ Event Detail ──▶ Pilih Tiket
                                                    │
                    ┌───────────────────────────────┘
                    ▼
              Checkout Page ──▶ Apply Voucher ──▶ Payment
                                                    │
                    ┌───────────────────────────────┘
                    ▼
              Payment Page ──▶ Success/Failed ──▶ E-Ticket
```

Lihat file `UX_FLOW.md` untuk wireframe dan detail lengkap.

---

## ✅ Best Practices

### Security

- ✅ Webhook signature verification
- ✅ Idempotency key untuk payment
- ✅ Rate limiting pada checkout
- ✅ CSRF protection
- ✅ Input sanitization
- ✅ SQL injection prevention (Eloquent)

### Performance

- ✅ Database indexing
- ✅ Query optimization (eager loading)
- ✅ Redis caching
- ✅ Queue untuk heavy tasks
- ✅ CDN untuk static assets
- ✅ Image optimization

### Reliability

- ✅ Database transactions
- ✅ Pessimistic locking untuk stock
- ✅ Dead letter queue
- ✅ Comprehensive logging
- ✅ Health checks

---

## 🚀 Getting Started

### Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- PostgreSQL 15+
- Redis 7+

### Installation

```bash
# Clone repository
git clone https://github.com/ngevent/ngevent.git
cd ngevent

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve
```

### Environment Variables

```env
# Application
APP_NAME=ngevent.id
APP_ENV=production
APP_URL=https://ngevent.id

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ngevent
DB_USERNAME=ngevent
DB_PASSWORD=secret

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Xendit
XENDIT_SECRET_KEY=xnd_production_xxx
XENDIT_PUBLIC_KEY=xnd_public_xxx
XENDIT_WEBHOOK_TOKEN=xxx

# Platform Config
PLATFORM_FEE_PERCENTAGE=5
SETTLEMENT_DELAY_DAYS=7
```

---

## 📚 Documentation Files

| File | Deskripsi |
|------|-----------|
| `README.md` | Overview dan quick start |
| `ARCHITECTURE.md` | Detail arsitektur sistem |
| `DATABASE.md` | Schema dan relasi database |
| `PAYMENT_FLOW.md` | Alur pembayaran dan Xendit integration |
| `UX_FLOW.md` | User experience dan wireframe |
| `API.md` | API documentation (jika ada) |

---

## 📄 License

Proprietary - ngevent.id © 2024

---

## 👥 Contributors

- Backend: Senior Laravel Developer
- Frontend: UI/UX Designer + Frontend Developer
- DevOps: Infrastructure Engineer


Ben ketok pronuktif wok part 3
