<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'xendit_external_id',
        'amount',
        'payment_method',
        'payment_channel',
        'status',
        'paid_at',
        'expires_at',
        'paid_amount',
        'adjusted_received_amount',
        'fees_paid_amount',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'integer',
            'paid_amount' => 'integer',
            'adjusted_received_amount' => 'integer',
            'fees_paid_amount' => 'integer',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'raw_response' => 'array',
        ];
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Status Checks
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function isExpired(): bool
    {
        if ($this->status === PaymentStatus::EXPIRED) {
            return true;
        }
        
        return $this->expires_at && $this->expires_at->isPast() && !$this->isPaid();
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === PaymentStatus::REFUNDED;
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): ?string
    {
        if (!$this->payment_method) {
            return null;
        }

        return match ($this->payment_method) {
            'CREDIT_CARD' => 'Kartu Kredit',
            'BANK_TRANSFER' => 'Transfer Bank',
            'EWALLET' => 'E-Wallet',
            'QR_CODE' => 'QRIS',
            'RETAIL_OUTLET' => 'Retail',
            default => $this->payment_method,
        };
    }

    public function getPaymentChannelLabelAttribute(): ?string
    {
        if (!$this->payment_channel) {
            return null;
        }

        return match ($this->payment_channel) {
            'BCA' => 'BCA',
            'BNI' => 'BNI',
            'BRI' => 'BRI',
            'MANDIRI' => 'Mandiri',
            'PERMATA' => 'Permata',
            'BSI' => 'BSI',
            'OVO' => 'OVO',
            'DANA' => 'DANA',
            'LINKAJA' => 'LinkAja',
            'SHOPEEPAY' => 'ShopeePay',
            'QRIS' => 'QRIS',
            default => $this->payment_channel,
        };
    }

    public function getRemainingTimeAttribute(): ?int
    {
        if (!$this->expires_at || $this->isPaid()) {
            return null;
        }
        
        $remaining = $this->expires_at->diffInSeconds(now(), false);
        return $remaining > 0 ? null : abs($remaining);
    }
}
