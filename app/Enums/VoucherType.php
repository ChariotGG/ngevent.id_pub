<?php

namespace App\Enums;

enum VoucherType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Nominal Tetap',
            self::PERCENTAGE => 'Persentase',
        };
    }

    public function calculateDiscount(int $subtotal, int $value, ?int $maxDiscount = null): int
    {
        $discount = match ($this) {
            self::FIXED => $value,
            self::PERCENTAGE => (int) ceil($subtotal * $value / 100),
        };

        if ($maxDiscount !== null && $discount > $maxDiscount) {
            return $maxDiscount;
        }

        return min($discount, $subtotal);
    }

    public function formatValue(int $value): string
    {
        return match ($this) {
            self::FIXED => 'Rp ' . number_format($value, 0, ',', '.'),
            self::PERCENTAGE => $value . '%',
        };
    }
}
