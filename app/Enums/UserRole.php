<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case ORGANIZER = 'organizer';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::ORGANIZER => 'Penyelenggara',
            self::USER => 'Pengguna',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'red',
            self::ORGANIZER => 'blue',
            self::USER => 'gray',
        };
    }
}
