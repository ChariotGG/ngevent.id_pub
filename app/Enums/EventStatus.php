<?php

namespace App\Enums;

enum EventStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Dipublikasi',
            self::COMPLETED => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'green',
            self::COMPLETED => 'blue',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'pencil',
            self::PUBLISHED => 'globe',
            self::COMPLETED => 'check-circle',
        };
    }

    public function canEdit(): bool
    {
        return $this === self::DRAFT;
    }

    public function canDelete(): bool
    {
        return $this === self::DRAFT;
    }

    public function canPublish(): bool
    {
        return $this === self::DRAFT;
    }

    public function canUnpublish(): bool
    {
        return $this === self::PUBLISHED;
    }
}
