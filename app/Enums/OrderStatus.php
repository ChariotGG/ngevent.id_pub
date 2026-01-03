<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case PAID = 'paid';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::AWAITING_PAYMENT => 'Menunggu Pembayaran',
            self::PAID => 'Dibayar',
            self::COMPLETED => 'Selesai',
            self::EXPIRED => 'Kedaluwarsa',
            self::CANCELLED => 'Dibatalkan',
            self::REFUNDED => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::AWAITING_PAYMENT => 'yellow',
            self::PAID => 'green',
            self::COMPLETED => 'blue',
            self::EXPIRED => 'red',
            self::CANCELLED => 'red',
            self::REFUNDED => 'orange',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'clock',
            self::AWAITING_PAYMENT => 'credit-card',
            self::PAID => 'check-circle',
            self::COMPLETED => 'badge-check',
            self::EXPIRED => 'x-circle',
            self::CANCELLED => 'ban',
            self::REFUNDED => 'receipt-refund',
        };
    }

    public function isPaid(): bool
    {
        return in_array($this, [self::PAID, self::COMPLETED]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::EXPIRED, self::CANCELLED, self::REFUNDED]);
    }

    public function canRefund(): bool
    {
        return in_array($this, [self::PAID, self::COMPLETED]);
    }
}
