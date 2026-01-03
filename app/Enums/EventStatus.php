<?php

namespace App\Enums;

enum EventStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case APPROVED = 'approved';
    case PUBLISHED = 'published';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Menunggu Review',
            self::APPROVED => 'Disetujui',
            self::PUBLISHED => 'Dipublikasi',
            self::CANCELLED => 'Dibatalkan',
            self::COMPLETED => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_REVIEW => 'yellow',
            self::APPROVED => 'blue',
            self::PUBLISHED => 'green',
            self::CANCELLED => 'red',
            self::COMPLETED => 'indigo',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'pencil',
            self::PENDING_REVIEW => 'clock',
            self::APPROVED => 'check',
            self::PUBLISHED => 'globe',
            self::CANCELLED => 'x-circle',
            self::COMPLETED => 'check-circle',
        };
    }

    public static function publicStatuses(): array
    {
        return [self::PUBLISHED, self::COMPLETED];
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::DRAFT, self::PENDING_REVIEW, self::APPROVED]);
    }

    public function canDelete(): bool
    {
        return $this === self::DRAFT;
    }

    public function canPublish(): bool
    {
        return $this === self::APPROVED;
    }
}
