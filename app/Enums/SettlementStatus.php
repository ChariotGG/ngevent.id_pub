<?php

namespace App\Enums;

enum SettlementStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case TRANSFERRED = 'transferred';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::PROCESSING => 'Diproses',
            self::TRANSFERRED => 'Ditransfer',
            self::FAILED => 'Gagal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::TRANSFERRED => 'green',
            self::FAILED => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'clock',
            self::PROCESSING => 'arrow-path',
            self::TRANSFERRED => 'check-circle',
            self::FAILED => 'x-circle',
        };
    }

    public function canProcess(): bool
    {
        return $this === self::PENDING;
    }

    public function canRetry(): bool
    {
        return $this === self::FAILED;
    }
}
