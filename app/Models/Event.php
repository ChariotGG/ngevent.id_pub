<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'organizer_id',
        'category_id',
        'title',
        'slug',
        'description',
        'short_description',
        'poster',
        'banner',
        'gallery',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'timezone',
        'venue_name',
        'venue_address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'is_online',
        'online_url',
        'status',
        'is_featured',
        'is_free',
        'min_price',
        'max_price',
        'proposal_file',
        'admin_notes',
        'rejection_reason',
        'published_at',
        'approved_at',
        'approved_by',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'is_online' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'published_at' => 'datetime',
            'approved_at' => 'datetime',
            'min_price' => 'integer',
            'max_price' => 'integer',
            'views_count' => 'integer',
        ];
    }

    protected function slugSource(): string
    {
        return 'title';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', EventStatus::PUBLISHED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('venue_name', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }

    // Status Checks
    public function isPublished(): bool
    {
        return $this->status === EventStatus::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === EventStatus::DRAFT;
    }

    public function isPendingReview(): bool
    {
        return $this->status === EventStatus::PENDING_REVIEW;
    }

    public function isCompleted(): bool
    {
        return $this->status === EventStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === EventStatus::CANCELLED;
    }

    public function isMultiDay(): bool
    {
        return !$this->start_date->eq($this->end_date);
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture() || $this->start_date->isToday();
    }

    public function isOngoing(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    public function hasOrders(): bool
    {
        return $this->orders()->exists();
    }

    public function hasPaidOrders(): bool
    {
        return $this->orders()->whereIn('status', ['paid', 'completed'])->exists();
    }

    // Accessors
    public function getPosterUrlAttribute(): string
    {
        if ($this->poster) {
            return asset('storage/' . $this->poster);
        }

        return asset('images/default-event-poster.jpg');
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }

    public function getFormattedDateAttribute(): string
    {
        if ($this->isMultiDay()) {
            if ($this->start_date->month === $this->end_date->month) {
                return $this->start_date->format('d') . ' - ' . $this->end_date->format('d M Y');
            }
            return $this->start_date->format('d M') . ' - ' . $this->end_date->format('d M Y');
        }

        return $this->start_date->format('d M Y');
    }

    public function getFormattedTimeAttribute(): ?string
    {
        if (!$this->start_time) {
            return null;
        }

        $start = \Carbon\Carbon::parse($this->start_time)->format('H:i');

        if ($this->end_time) {
            $end = \Carbon\Carbon::parse($this->end_time)->format('H:i');
            return "{$start} - {$end} WIB";
        }

        return "{$start} WIB";
    }

    public function getFormattedLocationAttribute(): string
    {
        $parts = array_filter([$this->venue_name, $this->city]);
        return implode(', ', $parts) ?: 'Online Event';
    }

    public function getLowestPriceAttribute(): int
    {
        return $this->tickets()
            ->join('ticket_variants', 'tickets.id', '=', 'ticket_variants.ticket_id')
            ->where('ticket_variants.is_active', true)
            ->min('ticket_variants.price') ?? 0;
    }

    public function getHighestPriceAttribute(): int
    {
        return $this->tickets()
            ->join('ticket_variants', 'tickets.id', '=', 'ticket_variants.ticket_id')
            ->where('ticket_variants.is_active', true)
            ->max('ticket_variants.price') ?? 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) {
            return 'Gratis';
        }

        $lowest = $this->lowest_price;
        $highest = $this->highest_price;

        if ($lowest === $highest) {
            return 'Rp ' . number_format($lowest, 0, ',', '.');
        }

        return 'Rp ' . number_format($lowest, 0, ',', '.') . ' - Rp ' . number_format($highest, 0, ',', '.');
    }

    // Stats
    public function getTotalTicketsSoldAttribute(): int
    {
        return $this->orders()
            ->whereIn('status', ['paid', 'completed'])
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->sum('order_items.quantity');
    }

    public function getTotalRevenueAttribute(): int
    {
        return $this->orders()
            ->whereIn('status', ['paid', 'completed'])
            ->sum('subtotal');
    }

    public function getAvailableTicketsCountAttribute(): int
    {
        return $this->tickets()
            ->join('ticket_variants', 'tickets.id', '=', 'ticket_variants.ticket_id')
            ->where('ticket_variants.is_active', true)
            ->selectRaw('SUM(ticket_variants.stock - ticket_variants.sold_count - ticket_variants.reserved_count) as available')
            ->value('available') ?? 0;
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function updatePriceRange(): void
    {
        $this->update([
            'min_price' => $this->lowest_price,
            'max_price' => $this->highest_price,
        ]);
    }
}
