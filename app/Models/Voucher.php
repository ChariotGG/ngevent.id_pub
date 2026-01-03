<?php

namespace App\Models;

use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'usage_count',
        'usage_limit_per_user',
        'event_id',
        'category_id',
        'starts_at',
        'expires_at',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => VoucherType::class,
            'value' => 'integer',
            'min_purchase' => 'integer',
            'max_discount' => 'integer',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'usage_limit_per_user' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('usage_count < usage_limit');
            });
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where(function ($q) use ($eventId) {
            $q->whereNull('event_id')
              ->orWhere('event_id', $eventId);
        });
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->whereNull('category_id')
              ->orWhere('category_id', $categoryId);
        });
    }

    // Checks
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasReachedLimit(): bool
    {
        return $this->usage_limit !== null && $this->usage_count >= $this->usage_limit;
    }

    public function hasUserReachedLimit(int $userId): bool
    {
        if ($this->usage_limit_per_user === null) {
            return false;
        }

        $userUsage = $this->usages()->where('user_id', $userId)->count();
        return $userUsage >= $this->usage_limit_per_user;
    }

    public function isApplicableToEvent(Event $event): bool
    {
        if ($this->event_id && $this->event_id !== $event->id) {
            return false;
        }

        if ($this->category_id && $this->category_id !== $event->category_id) {
            return false;
        }

        return true;
    }

    public function isApplicableToAmount(int $subtotal): bool
    {
        return $this->min_purchase === null || $subtotal >= $this->min_purchase;
    }

    public function canBeUsedBy(int $userId, Event $event, int $subtotal): array
    {
        if (!$this->isActive()) {
            return [false, 'Voucher tidak aktif atau sudah kedaluwarsa'];
        }

        if ($this->hasReachedLimit()) {
            return [false, 'Voucher sudah mencapai batas penggunaan'];
        }

        if ($this->hasUserReachedLimit($userId)) {
            return [false, 'Anda sudah mencapai batas penggunaan voucher ini'];
        }

        if (!$this->isApplicableToEvent($event)) {
            return [false, 'Voucher tidak berlaku untuk event ini'];
        }

        if (!$this->isApplicableToAmount($subtotal)) {
            return [false, 'Minimum pembelian belum tercapai'];
        }

        return [true, null];
    }

    // Calculations
    public function calculateDiscount(int $subtotal): int
    {
        return $this->type->calculateDiscount($subtotal, $this->value, $this->max_discount);
    }

    // Accessors
    public function getFormattedValueAttribute(): string
    {
        return $this->type->formatValue($this->value);
    }

    public function getFormattedMinPurchaseAttribute(): ?string
    {
        if (!$this->min_purchase) {
            return null;
        }
        
        return 'Rp ' . number_format($this->min_purchase, 0, ',', '.');
    }

    public function getFormattedMaxDiscountAttribute(): ?string
    {
        if (!$this->max_discount) {
            return null;
        }
        
        return 'Rp ' . number_format($this->max_discount, 0, ',', '.');
    }

    public function getRemainingUsageAttribute(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }
        
        return max(0, $this->usage_limit - $this->usage_count);
    }

    // Methods
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function decrementUsage(): void
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }
}
