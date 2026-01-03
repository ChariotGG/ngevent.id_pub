<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizer extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'logo',
        'banner',
        'bio',
        'website',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_verified',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected function slugSource(): string
    {
        return 'name';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(OrganizerSocialLink::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    // Accessors
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563EB&color=fff&size=200';
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }

    // Stats
    public function getTotalEventsAttribute(): int
    {
        return $this->events()->count();
    }

    public function getPublishedEventsCountAttribute(): int
    {
        return $this->events()->where('status', 'published')->count();
    }

    public function getTotalTicketsSoldAttribute(): int
    {
        return $this->events()
            ->join('orders', 'events.id', '=', 'orders.event_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->sum('order_items.quantity');
    }

    public function getTotalRevenueAttribute(): int
    {
        return $this->events()
            ->join('orders', 'events.id', '=', 'orders.event_id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->sum('orders.subtotal');
    }
}
