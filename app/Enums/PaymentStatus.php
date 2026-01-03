<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::PAID => 'Dibayar',
            self::EXPIRED => 'Kedaluwarsa',
            self::FAILED => 'Gagal',
            self::REFUNDED => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PAID => 'green',
            self::EXPIRED => 'gray',
            self::FAILED => 'red',
            self::REFUNDED => 'orange',
        };
    }

    public function isSuccessful(): bool
    {
        return $this === self::PAID;
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::PAID, self::EXPIRED, self::FAILED, self::REFUNDED]);
    }
}
