<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'event_id',
        'voucher_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'subtotal',
        'discount',
        'platform_fee',
        'payment_fee',
        'total',
        'status',
        'notes',
        'paid_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
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
    }

    // Relationships
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

    // Scopes
    public function scopePaid($query)
    {
        return $query->whereIn('status', [OrderStatus::PAID, OrderStatus::COMPLETED]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT]);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', OrderStatus::EXPIRED);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    // Status Checks
    public function isPending(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT]);
    }

    public function isPaid(): bool
    {
        return in_array($this->status, [OrderStatus::PAID, OrderStatus::COMPLETED]);
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    public function isExpired(): bool
    {
        if ($this->status === OrderStatus::EXPIRED) {
            return true;
        }
        
        return $this->expires_at && $this->expires_at->isPast() && !$this->isPaid();
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    public function isRefunded(): bool
    {
        return $this->status === OrderStatus::REFUNDED;
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    public function canPay(): bool
    {
        return $this->status === OrderStatus::AWAITING_PAYMENT && !$this->isExpired();
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT]);
    }

    public function canRefund(): bool
    {
        return $this->status->canRefund();
    }

    // Accessors
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getFormattedPlatformFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->platform_fee, 0, ',', '.');
    }

    public function getFormattedPaymentFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->payment_fee, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getNetAmountAttribute(): int
    {
        return $this->subtotal - $this->discount - $this->platform_fee;
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getRemainingTimeAttribute(): ?int
    {
        if (!$this->expires_at || $this->isPaid()) {
            return null;
        }
        
        $remaining = $this->expires_at->diffInSeconds(now(), false);
        return $remaining > 0 ? null : abs($remaining);
    }

    public function getPaymentUrlAttribute(): ?string
    {
        return $this->payment?->xendit_invoice_url;
    }

    // Helpers
    public static function generateOrderNumber(): string
    {
        $prefix = 'NGE';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        
        return "{$prefix}{$date}{$random}";
    }
}
