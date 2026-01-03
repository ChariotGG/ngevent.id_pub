<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'event_day_id',
        'name',
        'price',
        'original_price',
        'stock',
        'sold_count',
        'reserved_count',
        'min_per_order',
        'max_per_order',
        'sale_start_at',
        'sale_end_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'original_price' => 'integer',
            'stock' => 'integer',
            'sold_count' => 'integer',
            'reserved_count' => 'integer',
            'min_per_order' => 'integer',
            'max_per_order' => 'integer',
            'is_active' => 'boolean',
            'sale_start_at' => 'datetime',
            'sale_end_at' => 'datetime',
        ];
    }

    // Relationships
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->whereRaw('stock - sold_count - reserved_count > 0')
            ->where(function ($q) {
                $q->whereNull('sale_start_at')
                  ->orWhere('sale_start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('sale_end_at')
                  ->orWhere('sale_end_at', '>=', now());
            });
    }

    // Accessors
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->sold_count - $this->reserved_count);
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === 0) {
            return 'Gratis';
        }
        
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (!$this->original_price || $this->original_price <= $this->price) {
            return null;
        }
        
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->original_price || $this->original_price <= $this->price) {
            return null;
        }
        
        return (int) round((1 - $this->price / $this->original_price) * 100);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->ticket->name];
        
        if ($this->name) {
            $parts[] = $this->name;
        }
        
        if ($this->eventDay) {
            $parts[] = $this->eventDay->short_date;
        }
        
        return implode(' - ', $parts);
    }

    // Checks
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->available_stock <= 0) {
            return false;
        }
        
        if ($this->sale_start_at && $this->sale_start_at->isFuture()) {
            return false;
        }
        
        if ($this->sale_end_at && $this->sale_end_at->isPast()) {
            return false;
        }
        
        return true;
    }

    public function isOnSale(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function isSoldOut(): bool
    {
        return $this->available_stock <= 0;
    }

    public function isSaleNotStarted(): bool
    {
        return $this->sale_start_at && $this->sale_start_at->isFuture();
    }

    public function isSaleEnded(): bool
    {
        return $this->sale_end_at && $this->sale_end_at->isPast();
    }

    // Methods
    public function canPurchase(int $quantity): bool
    {
        if ($quantity < ($this->min_per_order ?? 1)) {
            return false;
        }
        
        if ($this->max_per_order && $quantity > $this->max_per_order) {
            return false;
        }
        
        return $this->available_stock >= $quantity;
    }

    public function reserve(int $quantity): bool
    {
        if ($this->available_stock < $quantity) {
            return false;
        }
        
        $this->increment('reserved_count', $quantity);
        return true;
    }

    public function releaseReservation(int $quantity): void
    {
        $this->decrement('reserved_count', min($quantity, $this->reserved_count));
    }

    public function confirmSale(int $quantity): void
    {
        $this->decrement('reserved_count', $quantity);
        $this->increment('sold_count', $quantity);
    }
}
