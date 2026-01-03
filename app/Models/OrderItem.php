<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ticket_id',
        'ticket_variant_id',
        'ticket_name',
        'variant_name',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function ticketVariant(): BelongsTo
    {
        return $this->belongsTo(TicketVariant::class);
    }

    public function issuedTickets(): HasMany
    {
        return $this->hasMany(IssuedTicket::class);
    }

    // Accessors
    public function getFormattedUnitPriceAttribute(): string
    {
        if ($this->unit_price === 0) {
            return 'Gratis';
        }
        
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->ticket_name];
        
        if ($this->variant_name) {
            $parts[] = $this->variant_name;
        }
        
        return implode(' - ', $parts);
    }

    // Methods
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            $item->subtotal = $item->unit_price * $item->quantity;
        });
    }
}
