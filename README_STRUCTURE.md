# 📁 Struktur Proyek ngevent.id

## Overview

Platform ticketing event Indonesia menggunakan Laravel 11 dengan arsitektur Service-Repository pattern.

---

## 🗂️ Struktur Direktori Lengkap

```
ngevent/
├── 📄 .env.example
├── 📄 .gitignore
├── 📄 composer.json
├── 📄 package.json
├── 📄 vite.config.js
├── 📄 tailwind.config.js
├── 📄 postcss.config.js
├── 📄 phpunit.xml
├── 📄 README.md
├── 📄 STRUCTURE.md
│
├── 📁 app/
│   ├── 📁 Enums/
│   │   ├── 📄 EventStatus.php
│   │   ├── 📄 OrderStatus.php
│   │   ├── 📄 PaymentStatus.php
│   │   ├── 📄 SettlementStatus.php
│   │   ├── 📄 TicketType.php
│   │   ├── 📄 UserRole.php
│   │   └── 📄 VoucherType.php
│   │
│   ├── 📁 Exceptions/
│   │   ├── 📄 Handler.php
│   │   ├── 📄 InsufficientTicketException.php
│   │   ├── 📄 PaymentFailedException.php
│   │   └── 📄 VoucherInvalidException.php
│   │
│   ├── 📁 Events/
│   │   ├── 📄 OrderCreated.php
│   │   ├── 📄 OrderPaid.php
│   │   ├── 📄 OrderExpired.php
│   │   ├── 📄 OrderRefunded.php
│   │   ├── 📄 TicketIssued.php
│   │   └── 📄 SettlementProcessed.php
│   │
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── 📄 Controller.php
│   │   │   │
│   │   │   ├── 📁 Admin/
│   │   │   │   ├── 📄 DashboardController.php
│   │   │   │   ├── 📄 EventController.php
│   │   │   │   ├── 📄 CategoryController.php
│   │   │   │   ├── 📄 SubcategoryController.php
│   │   │   │   ├── 📄 UserController.php
│   │   │   │   ├── 📄 OrganizerController.php
│   │   │   │   ├── 📄 VoucherController.php
│   │   │   │   ├── 📄 SettlementController.php
│   │   │   │   └── 📄 ReportController.php
│   │   │   │
│   │   │   ├── 📁 Organizer/
│   │   │   │   ├── 📄 DashboardController.php
│   │   │   │   ├── 📄 EventController.php
│   │   │   │   ├── 📄 TicketController.php
│   │   │   │   ├── 📄 OrderController.php
│   │   │   │   ├── 📄 AttendeeController.php
│   │   │   │   ├── 📄 SettlementController.php
│   │   │   │   ├── 📄 ProfileController.php
│   │   │   │   └── 📄 ReportController.php
│   │   │   │
│   │   │   ├── 📁 User/
│   │   │   │   ├── 📄 HomeController.php
│   │   │   │   ├── 📄 EventController.php
│   │   │   │   ├── 📄 CheckoutController.php
│   │   │   │   ├── 📄 PaymentController.php
│   │   │   │   ├── 📄 OrderController.php
│   │   │   │   ├── 📄 TicketController.php
│   │   │   │   └── 📄 ProfileController.php
│   │   │   │
│   │   │   ├── 📁 Auth/
│   │   │   │   ├── 📄 LoginController.php
│   │   │   │   ├── 📄 RegisterController.php
│   │   │   │   ├── 📄 ForgotPasswordController.php
│   │   │   │   ├── 📄 ResetPasswordController.php
│   │   │   │   └── 📄 VerifyEmailController.php
│   │   │   │
│   │   │   ├── 📁 Api/
│   │   │   │   ├── 📄 EventController.php
│   │   │   │   ├── 📄 TicketController.php
│   │   │   │   ├── 📄 VoucherController.php
│   │   │   │   └── 📄 LocationController.php
│   │   │   │
│   │   │   └── 📁 Webhook/
│   │   │       └── 📄 XenditController.php
│   │   │
│   │   ├── 📁 Middleware/
│   │   │   ├── 📄 EnsureUserIsAdmin.php
│   │   │   ├── 📄 EnsureUserIsOrganizer.php
│   │   │   ├── 📄 EnsureEmailIsVerified.php
│   │   │   ├── 📄 XenditWebhookSignature.php
│   │   │   └── 📄 ThrottleCheckout.php
│   │   │
│   │   └── 📁 Requests/
│   │       ├── 📁 Admin/
│   │       │   ├── 📄 StoreCategoryRequest.php
│   │       │   ├── 📄 UpdateCategoryRequest.php
│   │       │   ├── 📄 StoreVoucherRequest.php
│   │       │   ├── 📄 UpdateVoucherRequest.php
│   │       │   ├── 📄 ApproveEventRequest.php
│   │       │   └── 📄 ProcessSettlementRequest.php
│   │       │
│   │       ├── 📁 Organizer/
│   │       │   ├── 📄 StoreEventRequest.php
│   │       │   ├── 📄 UpdateEventRequest.php
│   │       │   ├── 📄 StoreTicketRequest.php
│   │       │   ├── 📄 UpdateTicketRequest.php
│   │       │   ├── 📄 StoreTicketVariantRequest.php
│   │       │   └── 📄 UpdateProfileRequest.php
│   │       │
│   │       └── 📁 User/
│   │           ├── 📄 CheckoutRequest.php
│   │           ├── 📄 ApplyVoucherRequest.php
│   │           └── 📄 UpdateProfileRequest.php
│   │
│   ├── 📁 Jobs/
│   │   ├── 📄 ProcessPaymentWebhook.php
│   │   ├── 📄 ExpireUnpaidOrder.php
│   │   ├── 📄 GenerateIssuedTickets.php
│   │   ├── 📄 SendOrderConfirmation.php
│   │   ├── 📄 SendTicketEmail.php
│   │   ├── 📄 ProcessSettlement.php
│   │   ├── 📄 ProcessEventCompletion.php
│   │   └── 📄 RefundOrder.php
│   │
│   ├── 📁 Listeners/
│   │   ├── 📄 SendOrderConfirmationEmail.php
│   │   ├── 📄 GenerateTicketsAfterPayment.php
│   │   ├── 📄 UpdateTicketStockAfterPayment.php
│   │   ├── 📄 ReleaseTicketStockOnExpiry.php
│   │   ├── 📄 SendTicketIssuedNotification.php
│   │   ├── 📄 LogPaymentActivity.php
│   │   └── 📄 NotifyOrganizerOnSale.php
│   │
│   ├── 📁 Mail/
│   │   ├── 📄 OrderConfirmation.php
│   │   ├── 📄 TicketIssued.php
│   │   ├── 📄 PaymentReminder.php
│   │   ├── 📄 OrderRefunded.php
│   │   ├── 📄 EventApproved.php
│   │   ├── 📄 EventRejected.php
│   │   └── 📄 SettlementProcessed.php
│   │
│   ├── 📁 Models/
│   │   ├── 📄 User.php
│   │   ├── 📄 Organizer.php
│   │   ├── 📄 OrganizerSocialLink.php
│   │   ├── 📄 Category.php
│   │   ├── 📄 Subcategory.php
│   │   ├── 📄 Event.php
│   │   ├── 📄 EventDay.php
│   │   ├── 📄 Ticket.php
│   │   ├── 📄 TicketVariant.php
│   │   ├── 📄 Order.php
│   │   ├── 📄 OrderItem.php
│   │   ├── 📄 Payment.php
│   │   ├── 📄 IssuedTicket.php
│   │   ├── 📄 Voucher.php
│   │   ├── 📄 VoucherUsage.php
│   │   └── 📄 Settlement.php
│   │
│   ├── 📁 Notifications/
│   │   ├── 📄 OrderPaidNotification.php
│   │   ├── 📄 TicketIssuedNotification.php
│   │   ├── 📄 EventApprovedNotification.php
│   │   └── 📄 SettlementProcessedNotification.php
│   │
│   ├── 📁 Policies/
│   │   ├── 📄 EventPolicy.php
│   │   ├── 📄 TicketPolicy.php
│   │   ├── 📄 OrderPolicy.php
│   │   ├── 📄 OrganizerPolicy.php
│   │   ├── 📄 SettlementPolicy.php
│   │   └── 📄 VoucherPolicy.php
│   │
│   ├── 📁 Providers/
│   │   ├── 📄 AppServiceProvider.php
│   │   ├── 📄 AuthServiceProvider.php
│   │   ├── 📄 EventServiceProvider.php
│   │   └── 📄 RepositoryServiceProvider.php
│   │
│   ├── 📁 Repositories/
│   │   ├── 📁 Contracts/
│   │   │   ├── 📄 EventRepositoryInterface.php
│   │   │   ├── 📄 TicketRepositoryInterface.php
│   │   │   ├── 📄 OrderRepositoryInterface.php
│   │   │   ├── 📄 PaymentRepositoryInterface.php
│   │   │   ├── 📄 VoucherRepositoryInterface.php
│   │   │   └── 📄 SettlementRepositoryInterface.php
│   │   │
│   │   └── 📁 Eloquent/
│   │       ├── 📄 EventRepository.php
│   │       ├── 📄 TicketRepository.php
│   │       ├── 📄 OrderRepository.php
│   │       ├── 📄 PaymentRepository.php
│   │       ├── 📄 VoucherRepository.php
│   │       └── 📄 SettlementRepository.php
│   │
│   └── 📁 Services/
│       ├── 📄 EventService.php
│       ├── 📄 TicketService.php
│       ├── 📄 OrderService.php
│       ├── 📄 PaymentService.php
│       ├── 📄 VoucherService.php
│       ├── 📄 SettlementService.php
│       ├── 📄 CheckoutService.php
│       ├── 📄 ETicketService.php
│       │
│       └── 📁 Xendit/
│           ├── 📄 XenditClient.php
│           ├── 📄 InvoiceService.php
│           └── 📄 WebhookHandler.php
│
├── 📁 bootstrap/
│   ├── 📄 app.php
│   ├── 📄 providers.php
│   └── 📁 cache/
│       └── 📄 .gitignore
│
├── 📁 config/
│   ├── 📄 app.php
│   ├── 📄 auth.php
│   ├── 📄 cache.php
│   ├── 📄 database.php
│   ├── 📄 filesystems.php
│   ├── 📄 logging.php
│   ├── 📄 mail.php
│   ├── 📄 queue.php
│   ├── 📄 services.php
│   ├── 📄 session.php
│   ├── 📄 xendit.php
│   └── 📄 ngevent.php
│
├── 📁 database/
│   ├── 📄 .gitignore
│   │
│   ├── 📁 factories/
│   │   ├── 📄 UserFactory.php
│   │   ├── 📄 OrganizerFactory.php
│   │   ├── 📄 CategoryFactory.php
│   │   ├── 📄 EventFactory.php
│   │   ├── 📄 TicketFactory.php
│   │   ├── 📄 TicketVariantFactory.php
│   │   ├── 📄 OrderFactory.php
│   │   └── 📄 VoucherFactory.php
│   │
│   ├── 📁 migrations/
│   │   ├── 📄 0001_01_01_000000_create_users_table.php
│   │   ├── 📄 0001_01_01_000001_create_cache_table.php
│   │   ├── 📄 0001_01_01_000002_create_jobs_table.php
│   │   ├── 📄 2024_01_01_000001_create_organizers_table.php
│   │   ├── 📄 2024_01_01_000002_create_organizer_social_links_table.php
│   │   ├── 📄 2024_01_01_000003_create_categories_table.php
│   │   ├── 📄 2024_01_01_000004_create_subcategories_table.php
│   │   ├── 📄 2024_01_01_000005_create_events_table.php
│   │   ├── 📄 2024_01_01_000006_create_event_subcategory_table.php
│   │   ├── 📄 2024_01_01_000007_create_event_days_table.php
│   │   ├── 📄 2024_01_01_000008_create_tickets_table.php
│   │   ├── 📄 2024_01_01_000009_create_ticket_variants_table.php
│   │   ├── 📄 2024_01_01_000010_create_vouchers_table.php
│   │   ├── 📄 2024_01_01_000011_create_orders_table.php
│   │   ├── 📄 2024_01_01_000012_create_order_items_table.php
│   │   ├── 📄 2024_01_01_000013_create_payments_table.php
│   │   ├── 📄 2024_01_01_000014_create_issued_tickets_table.php
│   │   ├── 📄 2024_01_01_000015_create_voucher_usages_table.php
│   │   └── 📄 2024_01_01_000016_create_settlements_table.php
│   │
│   └── 📁 seeders/
│       ├── 📄 DatabaseSeeder.php
│       ├── 📄 UserSeeder.php
│       ├── 📄 CategorySeeder.php
│       ├── 📄 SubcategorySeeder.php
│       ├── 📄 OrganizerSeeder.php
│       ├── 📄 EventSeeder.php
│       └── 📄 VoucherSeeder.php
│
├── 📁 public/
│   ├── 📄 index.php
│   ├── 📄 robots.txt
│   ├── 📄 favicon.ico
│   ├── 📄 .htaccess
│   │
│   ├── 📁 css/
│   │   └── 📄 app.css
│   │
│   ├── 📁 js/
│   │   └── 📄 app.js
│   │
│   └── 📁 images/
│       ├── 📄 logo.svg
│       ├── 📄 logo-white.svg
│       ├── 📄 default-event-poster.jpg
│       ├── 📄 default-avatar.png
│       └── 📁 icons/
│           ├── 📄 category-cosplay.svg
│           ├── 📄 category-music.svg
│           └── 📄 category-sports.svg
│
├── 📁 resources/
│   ├── 📁 css/
│   │   └── 📄 app.css
│   │
│   ├── 📁 js/
│   │   ├── 📄 app.js
│   │   ├── 📄 bootstrap.js
│   │   │
│   │   └── 📁 components/
│   │       ├── 📄 countdown-timer.js
│   │       ├── 📄 ticket-selector.js
│   │       ├── 📄 image-upload.js
│   │       └── 📄 price-calculator.js
│   │
│   └── 📁 views/
│       ├── 📁 layouts/
│       │   ├── 📄 app.blade.php
│       │   ├── 📄 admin.blade.php
│       │   ├── 📄 organizer.blade.php
│       │   ├── 📄 auth.blade.php
│       │   └── 📄 guest.blade.php
│       │
│       ├── 📁 components/
│       │   ├── 📁 ui/
│       │   │   ├── 📄 button.blade.php
│       │   │   ├── 📄 input.blade.php
│       │   │   ├── 📄 textarea.blade.php
│       │   │   ├── 📄 select.blade.php
│       │   │   ├── 📄 checkbox.blade.php
│       │   │   ├── 📄 radio.blade.php
│       │   │   ├── 📄 badge.blade.php
│       │   │   ├── 📄 alert.blade.php
│       │   │   ├── 📄 modal.blade.php
│       │   │   ├── 📄 dropdown.blade.php
│       │   │   ├── 📄 card.blade.php
│       │   │   ├── 📄 pagination.blade.php
│       │   │   └── 📄 loading.blade.php
│       │   │
│       │   ├── 📄 event-card.blade.php
│       │   ├── 📄 event-card-horizontal.blade.php
│       │   ├── 📄 ticket-card.blade.php
│       │   ├── 📄 ticket-selector.blade.php
│       │   ├── 📄 category-badge.blade.php
│       │   ├── 📄 category-pill.blade.php
│       │   ├── 📄 countdown-timer.blade.php
│       │   ├── 📄 price-display.blade.php
│       │   ├── 📄 order-summary.blade.php
│       │   ├── 📄 e-ticket.blade.php
│       │   ├── 📄 qr-code.blade.php
│       │   ├── 📄 organizer-card.blade.php
│       │   ├── 📄 search-bar.blade.php
│       │   ├── 📄 filter-sidebar.blade.php
│       │   ├── 📄 stat-card.blade.php
│       │   └── 📄 data-table.blade.php
│       │
│       ├── 📁 auth/
│       │   ├── 📄 login.blade.php
│       │   ├── 📄 register.blade.php
│       │   ├── 📄 register-organizer.blade.php
│       │   ├── 📄 forgot-password.blade.php
│       │   ├── 📄 reset-password.blade.php
│       │   ├── 📄 verify-email.blade.php
│       │   └── 📄 confirm-password.blade.php
│       │
│       ├── 📁 pages/
│       │   ├── 📁 home/
│       │   │   └── 📄 index.blade.php
│       │   │
│       │   ├── 📁 events/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 show.blade.php
│       │   │   └── 📄 category.blade.php
│       │   │
│       │   ├── 📁 checkout/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 payment.blade.php
│       │   │   ├── 📄 success.blade.php
│       │   │   ├── 📄 failed.blade.php
│       │   │   └── 📄 expired.blade.php
│       │   │
│       │   ├── 📁 tickets/
│       │   │   ├── 📄 index.blade.php
│       │   │   └── 📄 show.blade.php
│       │   │
│       │   ├── 📁 orders/
│       │   │   ├── 📄 index.blade.php
│       │   │   └── 📄 show.blade.php
│       │   │
│       │   ├── 📁 organizers/
│       │   │   └── 📄 show.blade.php
│       │   │
│       │   └── 📁 profile/
│       │       ├── 📄 index.blade.php
│       │       └── 📄 edit.blade.php
│       │
│       ├── 📁 admin/
│       │   ├── 📁 dashboard/
│       │   │   └── 📄 index.blade.php
│       │   │
│       │   ├── 📁 events/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 show.blade.php
│       │   │   ├── 📄 pending.blade.php
│       │   │   └── 📄 review.blade.php
│       │   │
│       │   ├── 📁 categories/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 create.blade.php
│       │   │   ├── 📄 edit.blade.php
│       │   │   └── 📄 subcategories.blade.php
│       │   │
│       │   ├── 📁 users/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 show.blade.php
│       │   │   └── 📄 edit.blade.php
│       │   │
│       │   ├── 📁 organizers/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 show.blade.php
│       │   │   └── 📄 verify.blade.php
│       │   │
│       │   ├── 📁 vouchers/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 create.blade.php
│       │   │   ├── 📄 edit.blade.php
│       │   │   └── 📄 show.blade.php
│       │   │
│       │   ├── 📁 settlements/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 show.blade.php
│       │   │   └── 📄 process.blade.php
│       │   │
│       │   └── 📁 reports/
│       │       ├── 📄 index.blade.php
│       │       ├── 📄 sales.blade.php
│       │       └── 📄 events.blade.php
│       │
│       ├── 📁 organizer/
│       │   ├── 📁 dashboard/
│       │   │   └── 📄 index.blade.php
│       │   │
│       │   ├── 📁 events/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 create.blade.php
│       │   │   ├── 📄 edit.blade.php
│       │   │   ├── 📄 show.blade.php
│       │   │   └── 📄 tickets.blade.php
│       │   │
│       │   ├── 📁 tickets/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 create.blade.php
│       │   │   └── 📄 edit.blade.php
│       │   │
│       │   ├── 📁 orders/
│       │   │   ├── 📄 index.blade.php
│       │   │   └── 📄 show.blade.php
│       │   │
│       │   ├── 📁 attendees/
│       │   │   ├── 📄 index.blade.php
│       │   │   ├── 📄 check-in.blade.php
│       │   │   └── 📄 scan.blade.php
│       │   │
│       │   ├── 📁 settlements/
│       │   │   ├── 📄 index.blade.php
│       │   │   └── 📄 show.blade.php
│       │   │
│       │   ├── 📁 reports/
│       │   │   ├── 📄 index.blade.php
│       │   │   └── 📄 sales.blade.php
│       │   │
│       │   └── 📁 profile/
│       │       ├── 📄 index.blade.php
│       │       └── 📄 edit.blade.php
│       │
│       ├── 📁 emails/
│       │   ├── 📄 order-confirmation.blade.php
│       │   ├── 📄 ticket-issued.blade.php
│       │   ├── 📄 payment-reminder.blade.php
│       │   ├── 📄 order-refunded.blade.php
│       │   ├── 📄 event-approved.blade.php
│       │   ├── 📄 event-rejected.blade.php
│       │   └── 📄 settlement-processed.blade.php
│       │
│       ├── 📁 pdf/
│       │   ├── 📄 e-ticket.blade.php
│       │   └── 📄 settlement-report.blade.php
│       │
│       └── 📁 errors/
│           ├── 📄 404.blade.php
│           ├── 📄 403.blade.php
│           ├── 📄 500.blade.php
│           └── 📄 503.blade.php
│
├── 📁 routes/
│   ├── 📄 web.php
│   ├── 📄 admin.php
│   ├── 📄 organizer.php
│   ├── 📄 api.php
│   ├── 📄 webhook.php
│   ├── 📄 auth.php
│   └── 📄 console.php
│
├── 📁 storage/
│   ├── 📁 app/
│   │   ├── 📁 public/
│   │   │   ├── 📁 events/
│   │   │   ├── 📁 organizers/
│   │   │   ├── 📁 users/
│   │   │   └── 📁 settlements/
│   │   └── 📄 .gitignore
│   │
│   ├── 📁 framework/
│   │   ├── 📁 cache/
│   │   ├── 📁 sessions/
│   │   ├── 📁 testing/
│   │   └── 📁 views/
│   │
│   └── 📁 logs/
│       ├── 📄 .gitignore
│       └── 📄 laravel.log
│
└── 📁 tests/
    ├── 📄 TestCase.php
    ├── 📄 CreatesApplication.php
    │
    ├── 📁 Feature/
    │   ├── 📁 Auth/
    │   │   ├── 📄 LoginTest.php
    │   │   ├── 📄 RegisterTest.php
    │   │   └── 📄 PasswordResetTest.php
    │   │
    │   ├── 📁 Event/
    │   │   ├── 📄 BrowseEventTest.php
    │   │   ├── 📄 CreateEventTest.php
    │   │   ├── 📄 UpdateEventTest.php
    │   │   └── 📄 PublishEventTest.php
    │   │
    │   ├── 📁 Checkout/
    │   │   ├── 📄 CheckoutFlowTest.php
    │   │   ├── 📄 VoucherApplicationTest.php
    │   │   └── 📄 StockManagementTest.php
    │   │
    │   ├── 📁 Payment/
    │   │   ├── 📄 XenditWebhookTest.php
    │   │   └── 📄 PaymentFlowTest.php
    │   │
    │   └── 📁 Settlement/
    │       └── 📄 SettlementProcessTest.php
    │
    └── 📁 Unit/
        ├── 📁 Services/
        │   ├── 📄 OrderServiceTest.php
        │   ├── 📄 VoucherServiceTest.php
        │   ├── 📄 TicketServiceTest.php
        │   └── 📄 SettlementServiceTest.php
        │
        └── 📁 Models/
            ├── 📄 EventTest.php
            ├── 📄 OrderTest.php
            ├── 📄 TicketVariantTest.php
            └── 📄 VoucherTest.php
```

---

## 📊 Ringkasan File

### Berdasarkan Tipe

| Kategori | Jumlah File |
|----------|-------------|
| Models | 16 |
| Controllers | 30 |
| Services | 11 |
| Repositories | 12 |
| Middleware | 5 |
| Form Requests | 13 |
| Jobs | 8 |
| Events | 6 |
| Listeners | 7 |
| Policies | 6 |
| Mail | 7 |
| Migrations | 18 |
| Seeders | 7 |
| Factories | 8 |
| Blade Views | 100+ |
| Config | 12 |
| Routes | 7 |
| Tests | 20+ |
| **Total** | **~280 files** |

---

## 🔗 Relasi Antar Modul

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    User      │────▶│  Organizer   │────▶│    Event     │
└──────────────┘     └──────────────┘     └──────────────┘
       │                                         │
       │                                         ▼
       │              ┌──────────────┐     ┌──────────────┐
       │              │   Category   │◀────│ Subcategory  │
       │              └──────────────┘     └──────────────┘
       │                    │
       ▼                    ▼
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    Order     │────▶│  OrderItem   │────▶│   Ticket     │
└──────────────┘     └──────────────┘     └──────────────┘
       │                    │                    │
       ▼                    ▼                    ▼
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   Payment    │     │IssuedTicket  │     │TicketVariant │
└──────────────┘     └──────────────┘     └──────────────┘
       │
       ▼
┌──────────────┐     ┌──────────────┐
│  Settlement  │     │   Voucher    │
└──────────────┘     └──────────────┘
```

---

## 🎯 Status Implementasi

### ✅ Sudah Dibuat
- [x] Semua Enums (7 files)
- [x] Semua Models (16 files)
- [x] composer.json
- [x] .env.example
- [x] STRUCTURE.md

### 🔄 Akan Dibuat
- [ ] Migrations (18 files)
- [ ] Services (11 files)
- [ ] Controllers (30 files)
- [ ] Repositories (12 files)
- [ ] Middleware (5 files)
- [ ] Form Requests (13 files)
- [ ] Jobs (8 files)
- [ ] Events & Listeners (13 files)
- [ ] Policies (6 files)
- [ ] Routes (7 files)
- [ ] Config files (12 files)
- [ ] Blade Views (100+ files)
- [ ] Seeders & Factories (15 files)
- [ ] Tests (20+ files)
- [ ] Frontend Assets (JS/CSS)

---

## 🚀 Cara Instalasi

```bash
# 1. Clone repository
git clone https://github.com/ngevent/platform.git
cd ngevent

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate
php artisan db:seed

# 5. Build assets
npm run build

# 6. Create storage link
php artisan storage:link

# 7. Run server
php artisan serve
```

---

## 📝 Catatan Pengembangan

### Konvensi Penamaan
- **Controllers**: PascalCase + Controller suffix (e.g., `EventController`)
- **Models**: PascalCase singular (e.g., `Event`, `TicketVariant`)
- **Tables**: snake_case plural (e.g., `events`, `ticket_variants`)
- **Routes**: kebab-case (e.g., `/events/my-event-slug`)
- **Views**: kebab-case dengan dot notation (e.g., `pages.events.show`)
- **Services**: PascalCase + Service suffix (e.g., `EventService`)

### Best Practices
1. **Fat Models, Thin Controllers** - Logic di Service layer
2. **Repository Pattern** - Abstraksi data access
3. **Form Requests** - Validasi terpisah dari controller
4. **Events & Listeners** - Decoupling untuk side effects
5. **Jobs** - Async processing untuk operasi berat
6. **Policies** - Authorization logic terpisah

---

*Dokumentasi ini akan diperbarui seiring progres pengembangan.*
