<?php

namespace App\Models;

use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'type',
        'benefits',
        'max_per_order',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => TicketType::class,
            'benefits' => 'array',
            'max_per_order' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
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

    public function availableVariants(): HasMany
    {
        return $this->activeVariants()
            ->whereRaw('stock - sold_count - reserved_count > 0');
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Accessors
    public function getLowestPriceAttribute(): int
    {
        return $this->variants()->where('is_active', true)->min('price') ?? 0;
    }

    public function getHighestPriceAttribute(): int
    {
        return $this->variants()->where('is_active', true)->max('price') ?? 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->type === TicketType::FREE) {
            return 'Gratis';
        }

        $lowest = $this->lowest_price;
        $highest = $this->highest_price;

        if ($lowest === $highest || $highest === 0) {
            return 'Rp ' . number_format($lowest, 0, ',', '.');
        }

        return 'Rp ' . number_format($lowest, 0, ',', '.') . ' - Rp ' . number_format($highest, 0, ',', '.');
    }

    public function getTotalStockAttribute(): int
    {
        return $this->variants()->sum('stock');
    }

    public function getTotalSoldAttribute(): int
    {
        return $this->variants()->sum('sold_count');
    }

    public function getTotalAvailableAttribute(): int
    {
        return $this->variants()
            ->where('is_active', true)
            ->selectRaw('SUM(stock - sold_count - reserved_count) as available')
            ->value('available') ?? 0;
    }

    // Checks
    public function hasAvailableStock(): bool
    {
        return $this->total_available > 0;
    }

    public function isFree(): bool
    {
        return $this->type === TicketType::FREE;
    }
}
