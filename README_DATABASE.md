# Database Schema - ngevent.id

## 1. Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                                    USERS                                         │
│  id │ name │ email │ password │ role │ phone │ avatar │ email_verified_at       │
└─────────────────────────────────────────────────────────────────────────────────┘
         │                                              │
         │ 1:1                                          │ 1:N
         ▼                                              ▼
┌─────────────────────────┐                   ┌─────────────────────────┐
│      ORGANIZERS         │                   │        ORDERS           │
│  id │ user_id │ name    │                   │  id │ user_id │ event_id│
│  slug │ logo │ bio      │                   │  total │ status │ ...   │
└─────────────────────────┘                   └─────────────────────────┘
         │                                              │
         │ 1:N                                          │ 1:N
         ▼                                              ▼
┌─────────────────────────┐                   ┌─────────────────────────┐
│        EVENTS           │                   │     ORDER_ITEMS         │
│  id │ organizer_id      │                   │  id │ order_id          │
│  title │ slug │ status  │                   │  ticket_variant_id      │
│  start_date │ end_date  │                   │  quantity │ subtotal    │
└─────────────────────────┘                   └─────────────────────────┘
    │           │                                       │
    │           │ N:N                                   │ 1:1
    │           ▼                                       ▼
    │    ┌──────────────┐                     ┌─────────────────────────┐
    │    │ CATEGORIES   │                     │    ISSUED_TICKETS       │
    │    │ SUBCATEGORIES│                     │  id │ order_item_id     │
    │    └──────────────┘                     │  code │ qr_code │ used  │
    │                                         └─────────────────────────┘
    │ 1:N
    ▼
┌─────────────────────────┐
│       TICKETS           │
│  id │ event_id │ name   │
│  type │ description     │
└─────────────────────────┘
         │
         │ 1:N
         ▼
┌─────────────────────────┐
│   TICKET_VARIANTS       │
│  id │ ticket_id         │
│  event_day_id │ price   │
│  stock │ sold_count     │
└─────────────────────────┘
```

## 2. Table Definitions

### 2.1 Users

```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    phone VARCHAR(20) NULL,
    avatar VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Role enum: 'admin', 'organizer', 'user'
```

**Laravel Migration:**

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('role', 20)->default('user');
    $table->string('phone', 20)->nullable();
    $table->string('avatar')->nullable();
    $table->rememberToken();
    $table->timestamps();
    
    $table->index('role');
});
```

### 2.2 Organizers

```sql
CREATE TABLE organizers (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255) NULL,
    banner VARCHAR(255) NULL,
    bio TEXT NULL,
    website VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    province VARCHAR(100) NULL,
    bank_name VARCHAR(100) NULL,
    bank_account_number VARCHAR(50) NULL,
    bank_account_name VARCHAR(255) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_organizers_user_id ON organizers(user_id);
CREATE INDEX idx_organizers_slug ON organizers(slug);
CREATE INDEX idx_organizers_is_verified ON organizers(is_verified);
```

**Laravel Migration:**

```php
Schema::create('organizers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('logo')->nullable();
    $table->string('banner')->nullable();
    $table->text('bio')->nullable();
    $table->string('website')->nullable();
    $table->string('email')->nullable();
    $table->string('phone', 20)->nullable();
    $table->text('address')->nullable();
    $table->string('city', 100)->nullable();
    $table->string('province', 100)->nullable();
    $table->string('bank_name', 100)->nullable();
    $table->string('bank_account_number', 50)->nullable();
    $table->string('bank_account_name')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->timestamp('verified_at')->nullable();
    $table->timestamps();
    
    $table->index('is_verified');
});
```

### 2.3 Organizer Social Links

```sql
CREATE TABLE organizer_social_links (
    id BIGSERIAL PRIMARY KEY,
    organizer_id BIGINT NOT NULL REFERENCES organizers(id) ON DELETE CASCADE,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_org_social_organizer ON organizer_social_links(organizer_id);

-- Platform: 'instagram', 'twitter', 'facebook', 'youtube', 'tiktok', 'website'
```

### 2.4 Categories

```sql
CREATE TABLE categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(100) NULL,
    color VARCHAR(20) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_is_active ON categories(is_active);
```

**Seed Data:**

```php
// CategorySeeder.php
$categories = [
    [
        'name' => 'Cosplay & Pop Culture',
        'slug' => 'cosplay-pop-culture',
        'icon' => 'sparkles',
        'color' => '#FF6B6B',
    ],
    [
        'name' => 'Music & Concert',
        'slug' => 'music-concert',
        'icon' => 'music',
        'color' => '#4ECDC4',
    ],
    [
        'name' => 'Sports',
        'slug' => 'sports',
        'icon' => 'trophy',
        'color' => '#45B7D1',
    ],
];
```

### 2.5 Subcategories

```sql
CREATE TABLE subcategories (
    id BIGSERIAL PRIMARY KEY,
    category_id BIGINT NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(category_id, slug)
);

CREATE INDEX idx_subcategories_category ON subcategories(category_id);
CREATE INDEX idx_subcategories_slug ON subcategories(slug);
```

**Seed Data (Music & Concert):**

```php
$musicSubcategories = [
    'Live Band', 'DJ Performance', 'Orchestra', 'Indie', 'Pop', 
    'Rock', 'Jazz', 'EDM', 'Mini Gig', 'Music Festival',
    'Meet & Greet', 'VIP Access', 'All Ages', '17+', '21+',
];

$cosplaySubcategories = [
    'Anime Expo', 'Comic Con', 'Cosplay Competition', 'Figure Exhibition',
    'Fan Meeting', 'Manga Festival', 'Gaming Convention', 'K-Pop Event',
];

$sportsSubcategories = [
    'Marathon', 'Fun Run', 'Tournament', 'Championship', 'Exhibition Match',
    'Esports', 'Fitness Event', 'Cycling', 'Swimming', 'Football',
];
```

### 2.6 Events

```sql
CREATE TABLE events (
    id BIGSERIAL PRIMARY KEY,
    organizer_id BIGINT NOT NULL REFERENCES organizers(id),
    category_id BIGINT NOT NULL REFERENCES categories(id),
    
    -- Basic Info
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    short_description VARCHAR(500) NULL,
    
    -- Media
    poster VARCHAR(255) NULL,
    banner VARCHAR(255) NULL,
    gallery JSONB NULL, -- Array of image URLs
    
    -- Date & Time
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    
    -- Location
    venue_name VARCHAR(255) NULL,
    venue_address TEXT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    is_online BOOLEAN DEFAULT FALSE,
    online_url VARCHAR(500) NULL,
    
    -- Settings
    status VARCHAR(20) DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    is_free BOOLEAN DEFAULT FALSE,
    requires_approval BOOLEAN DEFAULT FALSE,
    max_tickets_per_order INT DEFAULT 10,
    
    -- Documents
    proposal_file VARCHAR(255) NULL,
    terms_conditions TEXT NULL,
    
    -- Meta
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_events_organizer ON events(organizer_id);
CREATE INDEX idx_events_category ON events(category_id);
CREATE INDEX idx_events_slug ON events(slug);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_events_start_date ON events(start_date);
CREATE INDEX idx_events_city ON events(city);
CREATE INDEX idx_events_is_featured ON events(is_featured);
CREATE INDEX idx_events_published ON events(status, start_date) 
    WHERE status = 'published';

-- Status enum: 'draft', 'pending_review', 'approved', 'published', 'cancelled', 'completed'
```

**Laravel Migration:**

```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('organizer_id')->constrained();
    $table->foreignId('category_id')->constrained();
    
    // Basic Info
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('short_description', 500)->nullable();
    
    // Media
    $table->string('poster')->nullable();
    $table->string('banner')->nullable();
    $table->json('gallery')->nullable();
    
    // Date & Time
    $table->date('start_date');
    $table->date('end_date');
    $table->time('start_time')->nullable();
    $table->time('end_time')->nullable();
    $table->string('timezone', 50)->default('Asia/Jakarta');
    
    // Location
    $table->string('venue_name')->nullable();
    $table->text('venue_address')->nullable();
    $table->string('city', 100);
    $table->string('province', 100);
    $table->string('postal_code', 10)->nullable();
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->boolean('is_online')->default(false);
    $table->string('online_url', 500)->nullable();
    
    // Settings
    $table->string('status', 20)->default('draft');
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_free')->default(false);
    $table->boolean('requires_approval')->default(false);
    $table->integer('max_tickets_per_order')->default(10);
    
    // Documents
    $table->string('proposal_file')->nullable();
    $table->text('terms_conditions')->nullable();
    
    // Meta
    $table->unsignedInteger('view_count')->default(0);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    
    $table->index(['status', 'start_date']);
    $table->index('city');
    $table->index('is_featured');
});
```

### 2.7 Event Days

```sql
CREATE TABLE event_days (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    name VARCHAR(100) NULL, -- 'Day 1', 'Sabtu', etc.
    start_time TIME NULL,
    end_time TIME NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(event_id, date)
);

CREATE INDEX idx_event_days_event ON event_days(event_id);
CREATE INDEX idx_event_days_date ON event_days(date);
```

### 2.8 Event-Subcategory Pivot

```sql
CREATE TABLE event_subcategory (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    subcategory_id BIGINT NOT NULL REFERENCES subcategories(id) ON DELETE CASCADE,
    
    UNIQUE(event_id, subcategory_id)
);

CREATE INDEX idx_event_subcategory_event ON event_subcategory(event_id);
CREATE INDEX idx_event_subcategory_subcategory ON event_subcategory(subcategory_id);
```

### 2.9 Tickets

```sql
CREATE TABLE tickets (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'regular',
    description TEXT NULL,
    benefits JSONB NULL, -- Array of benefit strings
    terms TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_tickets_event ON tickets(event_id);
CREATE INDEX idx_tickets_type ON tickets(type);

-- Type enum: 'regular', 'vip', 'bundle', 'addon', 'free'
```

### 2.10 Ticket Variants

```sql
CREATE TABLE ticket_variants (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    event_day_id BIGINT NULL REFERENCES event_days(id) ON DELETE SET NULL,
    
    name VARCHAR(255) NOT NULL, -- 'Day 1 - Regular', 'Weekend Pass', etc.
    sku VARCHAR(50) NULL UNIQUE,
    price BIGINT NOT NULL DEFAULT 0, -- In cents/smallest currency unit
    original_price BIGINT NULL, -- For showing discount
    
    stock INT NOT NULL DEFAULT 0,
    sold_count INT DEFAULT 0,
    reserved_count INT DEFAULT 0,
    
    min_purchase INT DEFAULT 1,
    max_purchase INT DEFAULT 10,
    
    sale_start_at TIMESTAMP NULL,
    sale_end_at TIMESTAMP NULL,
    
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_ticket_variants_ticket ON ticket_variants(ticket_id);
CREATE INDEX idx_ticket_variants_event_day ON ticket_variants(event_day_id);
CREATE INDEX idx_ticket_variants_sku ON ticket_variants(sku);
CREATE INDEX idx_ticket_variants_active_sale ON ticket_variants(is_active, sale_start_at, sale_end_at);
```

**Laravel Migration:**

```php
Schema::create('ticket_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
    $table->foreignId('event_day_id')->nullable()->constrained()->nullOnDelete();
    
    $table->string('name');
    $table->string('sku', 50)->nullable()->unique();
    $table->unsignedBigInteger('price')->default(0);
    $table->unsignedBigInteger('original_price')->nullable();
    
    $table->unsignedInteger('stock')->default(0);
    $table->unsignedInteger('sold_count')->default(0);
    $table->unsignedInteger('reserved_count')->default(0);
    
    $table->unsignedTinyInteger('min_purchase')->default(1);
    $table->unsignedTinyInteger('max_purchase')->default(10);
    
    $table->timestamp('sale_start_at')->nullable();
    $table->timestamp('sale_end_at')->nullable();
    
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['is_active', 'sale_start_at', 'sale_end_at']);
});
```

### 2.11 Vouchers

```sql
CREATE TABLE vouchers (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    
    type VARCHAR(20) NOT NULL DEFAULT 'fixed',
    value BIGINT NOT NULL, -- Amount in cents or percentage
    min_purchase BIGINT NULL,
    max_discount BIGINT NULL, -- Cap for percentage voucher
    
    event_id BIGINT NULL REFERENCES events(id) ON DELETE CASCADE,
    category_id BIGINT NULL REFERENCES categories(id) ON DELETE SET NULL,
    
    usage_limit INT NULL,
    usage_count INT DEFAULT 0,
    usage_per_user INT DEFAULT 1,
    
    starts_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_vouchers_code ON vouchers(code);
CREATE INDEX idx_vouchers_event ON vouchers(event_id);
CREATE INDEX idx_vouchers_active ON vouchers(is_active, starts_at, expires_at);

-- Type enum: 'fixed', 'percentage'
```

### 2.12 Voucher Usages

```sql
CREATE TABLE voucher_usages (
    id BIGSERIAL PRIMARY KEY,
    voucher_id BIGINT NOT NULL REFERENCES vouchers(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    discount_amount BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_voucher_usages_voucher ON voucher_usages(voucher_id);
CREATE INDEX idx_voucher_usages_user ON voucher_usages(user_id);
CREATE UNIQUE INDEX idx_voucher_usages_order ON voucher_usages(order_id);
```

### 2.13 Orders

```sql
CREATE TABLE orders (
    id BIGSERIAL PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    user_id BIGINT NOT NULL REFERENCES users(id),
    event_id BIGINT NOT NULL REFERENCES events(id),
    voucher_id BIGINT NULL REFERENCES vouchers(id),
    
    -- Pricing (all in cents)
    subtotal BIGINT NOT NULL,
    discount BIGINT DEFAULT 0,
    platform_fee BIGINT DEFAULT 0,
    payment_fee BIGINT DEFAULT 0,
    total BIGINT NOT NULL,
    
    -- Status
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    
    -- Customer Info (snapshot)
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NULL,
    
    -- Notes
    notes TEXT NULL,
    cancel_reason TEXT NULL,
    
    -- Timestamps
    paid_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_orders_number ON orders(order_number);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_event ON orders(event_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_expires ON orders(expires_at) WHERE status IN ('pending', 'awaiting_payment');
CREATE INDEX idx_orders_created ON orders(created_at);

-- Status enum: 'pending', 'awaiting_payment', 'paid', 'completed', 'expired', 'cancelled', 'refunded'
```

### 2.14 Order Items

```sql
CREATE TABLE order_items (
    id BIGSERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    ticket_variant_id BIGINT NOT NULL REFERENCES ticket_variants(id),
    
    quantity INT NOT NULL,
    unit_price BIGINT NOT NULL,
    subtotal BIGINT NOT NULL,
    
    -- Snapshot ticket info
    ticket_name VARCHAR(255) NOT NULL,
    variant_name VARCHAR(255) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_variant ON order_items(ticket_variant_id);
```

### 2.15 Payments

```sql
CREATE TABLE payments (
    id BIGSERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    
    -- Xendit
    xendit_invoice_id VARCHAR(100) NULL UNIQUE,
    xendit_invoice_url VARCHAR(500) NULL,
    xendit_external_id VARCHAR(100) NULL,
    
    -- Payment Info
    amount BIGINT NOT NULL,
    payment_method VARCHAR(50) NULL,
    payment_channel VARCHAR(50) NULL,
    
    -- Status
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    
    -- Timestamps
    paid_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    
    -- Metadata
    metadata JSONB NULL,
    webhook_payload JSONB NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_payments_order ON payments(order_id);
CREATE INDEX idx_payments_xendit_invoice ON payments(xendit_invoice_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_external ON payments(xendit_external_id);

-- Status enum: 'pending', 'paid', 'expired', 'failed', 'refunded'
```

### 2.16 Issued Tickets

```sql
CREATE TABLE issued_tickets (
    id BIGSERIAL PRIMARY KEY,
    order_item_id BIGINT NOT NULL REFERENCES order_items(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id),
    
    code VARCHAR(50) NOT NULL UNIQUE, -- Unique ticket code
    qr_code TEXT NULL, -- QR code data or path
    
    -- Attendee Info
    attendee_name VARCHAR(255) NULL,
    attendee_email VARCHAR(255) NULL,
    attendee_phone VARCHAR(20) NULL,
    
    -- Usage
    is_used BOOLEAN DEFAULT FALSE,
    used_at TIMESTAMP NULL,
    used_by BIGINT NULL REFERENCES users(id), -- Staff who scanned
    
    -- Snapshot
    event_title VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    ticket_name VARCHAR(255) NOT NULL,
    variant_name VARCHAR(255) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX idx_issued_tickets_code ON issued_tickets(code);
CREATE INDEX idx_issued_tickets_order_item ON issued_tickets(order_item_id);
CREATE INDEX idx_issued_tickets_user ON issued_tickets(user_id);
CREATE INDEX idx_issued_tickets_is_used ON issued_tickets(is_used);
```

### 2.17 Settlements

```sql
CREATE TABLE settlements (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id),
    organizer_id BIGINT NOT NULL REFERENCES organizers(id),
    
    -- Amounts (all in cents)
    gross_amount BIGINT NOT NULL,
    platform_fee BIGINT NOT NULL,
    payment_fee_total BIGINT NOT NULL,
    refund_amount BIGINT DEFAULT 0,
    net_amount BIGINT NOT NULL,
    
    -- Bank Info (snapshot from organizer)
    bank_name VARCHAR(100) NOT NULL,
    bank_account_number VARCHAR(50) NOT NULL,
    bank_account_name VARCHAR(255) NOT NULL,
    
    -- Status
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    
    -- Transfer Info
    transfer_reference VARCHAR(100) NULL,
    transfer_proof VARCHAR(255) NULL,
    
    -- Notes
    notes TEXT NULL,
    
    -- Timestamps
    calculated_at TIMESTAMP NOT NULL,
    transferred_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_settlements_event ON settlements(event_id);
CREATE INDEX idx_settlements_organizer ON settlements(organizer_id);
CREATE INDEX idx_settlements_status ON settlements(status);

-- Status enum: 'pending', 'processing', 'transferred', 'failed'
```

## 3. Model Relationships

### 3.1 User Model

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\UserRole;

class User extends Authenticatable
{
    protected $casts = [
        'role' => UserRole::class,
        'email_verified_at' => 'datetime',
    ];

    public function organizer(): HasOne
    {
        return $this->hasOne(Organizer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function issuedTickets(): HasMany
    {
        return $this->hasMany(IssuedTicket::class);
    }

    public function voucherUsages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOrganizer(): bool
    {
        return $this->role === UserRole::ORGANIZER;
    }
}
```

### 3.2 Event Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\EventStatus;

class Event extends Model
{
    protected $casts = [
        'status' => EventStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'is_online' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(Subcategory::class, 'event_subcategory');
    }

    public function days(): HasMany
    {
        return $this->hasMany(EventDay::class)->orderBy('date');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(Settlement::class);
    }

    public function isPublished(): bool
    {
        return $this->status === EventStatus::PUBLISHED;
    }

    public function isMultiDay(): bool
    {
        return $this->start_date->ne($this->end_date);
    }

    public function hasOrders(): bool
    {
        return $this->orders()->exists();
    }

    public function getLowestPriceAttribute(): int
    {
        return $this->tickets()
            ->join('ticket_variants', 'tickets.id', '=', 'ticket_variants.ticket_id')
            ->where('ticket_variants.is_active', true)
            ->min('ticket_variants.price') ?? 0;
    }
}
```

### 3.3 Ticket & Variant Models

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketType;

class Ticket extends Model
{
    protected $casts = [
        'type' => TicketType::class,
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(TicketVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('sale_start_at')
                    ->orWhere('sale_start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('sale_end_at')
                    ->orWhere('sale_end_at', '>=', now());
            });
    }
}

class TicketVariant extends Model
{
    protected $casts = [
        'price' => 'integer',
        'original_price' => 'integer',
        'stock' => 'integer',
        'sold_count' => 'integer',
        'reserved_count' => 'integer',
        'is_active' => 'boolean',
        'sale_start_at' => 'datetime',
        'sale_end_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function eventDay(): BelongsTo
    {
        return $this->belongsTo(EventDay::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->sold_count - $this->reserved_count);
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->available_stock <= 0) return false;
        if ($this->sale_start_at && $this->sale_start_at->isFuture()) return false;
        if ($this->sale_end_at && $this->sale_end_at->isPast()) return false;
        return true;
    }
}
```

### 3.4 Order Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;

class Order extends Model
{
    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'integer',
        'discount' => 'integer',
        'platform_fee' => 'integer',
        'payment_fee' => 'integer',
        'total' => 'integer',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function voucherUsage(): HasOne
    {
        return $this->hasOne(VoucherUsage::class);
    }

    public function issuedTickets(): HasManyThrough
    {
        return $this->hasManyThrough(IssuedTicket::class, OrderItem::class);
    }

    public function isPaid(): bool
    {
        return in_array($this->status, [
            OrderStatus::PAID,
            OrderStatus::COMPLETED,
        ]);
    }

    public function isExpired(): bool
    {
        return $this->status === OrderStatus::EXPIRED 
            || ($this->expires_at && $this->expires_at->isPast());
    }
}
```

## 4. Indexes Summary

| Table | Index | Columns | Purpose |
|-------|-------|---------|---------|
| users | idx_users_email | email | Login lookup |
| users | idx_users_role | role | Role filtering |
| events | idx_events_slug | slug | URL lookup |
| events | idx_events_published | status, start_date | Homepage/listing |
| events | idx_events_city | city | Location filter |
| orders | idx_orders_number | order_number | Order lookup |
| orders | idx_orders_expires | expires_at | Expiration job |
| payments | idx_payments_xendit | xendit_invoice_id | Webhook lookup |
| issued_tickets | idx_issued_code | code | Ticket validation |
| vouchers | idx_vouchers_code | code | Voucher lookup |

## 5. Data Integrity Rules

### 5.1 Business Rules

1. Event tidak bisa dihapus jika sudah ada order
2. Tiket tidak bisa dihapus jika sudah ada order item
3. Stock tidak boleh negatif
4. Voucher usage tidak boleh melebihi limit
5. Order expires_at harus di-set saat status = pending/awaiting_payment

### 5.2 Database Constraints

```sql
-- Ensure stock consistency
ALTER TABLE ticket_variants ADD CONSTRAINT chk_stock_positive 
    CHECK (stock >= 0);

ALTER TABLE ticket_variants ADD CONSTRAINT chk_sold_count_positive 
    CHECK (sold_count >= 0);

ALTER TABLE ticket_variants ADD CONSTRAINT chk_reserved_positive 
    CHECK (reserved_count >= 0);

-- Ensure price consistency
ALTER TABLE ticket_variants ADD CONSTRAINT chk_price_positive 
    CHECK (price >= 0);

ALTER TABLE orders ADD CONSTRAINT chk_total_positive 
    CHECK (total >= 0);

-- Ensure event dates
ALTER TABLE events ADD CONSTRAINT chk_event_dates 
    CHECK (end_date >= start_date);
```
