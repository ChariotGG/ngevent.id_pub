<?php

namespace App\Enums;

enum TicketType: string
{
    case REGULAR = 'regular';
    case VIP = 'vip';
    case BUNDLE = 'bundle';
    case ADDON = 'addon';
    case FREE = 'free';

    public function label(): string
    {
        return match ($this) {
            self::REGULAR => 'Regular',
            self::VIP => 'VIP',
            self::BUNDLE => 'Bundle',
            self::ADDON => 'Add-on',
            self::FREE => 'Gratis',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REGULAR => 'blue',
            self::VIP => 'purple',
            self::BUNDLE => 'orange',
            self::ADDON => 'teal',
            self::FREE => 'green',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::REGULAR => 'ticket',
            self::VIP => 'star',
            self::BUNDLE => 'collection',
            self::ADDON => 'plus-circle',
            self::FREE => 'gift',
        };
    }

    public function isFree(): bool
    {
        return $this === self::FREE;
    }

    public function isPremium(): bool
    {
        return in_array($this, [self::VIP, self::BUNDLE]);
    }
}
