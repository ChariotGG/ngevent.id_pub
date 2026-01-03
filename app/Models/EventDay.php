<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'date',
        'name',
        'start_time',
        'end_time',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketVariants(): HasMany
    {
        return $this->hasMany(TicketVariant::class);
    }

    // Accessors
    public function getFormattedDateAttribute(): string
    {
        return $this->date->translatedFormat('l, d F Y');
    }

    public function getShortDateAttribute(): string
    {
        return $this->date->format('d M');
    }

    public function getDayNameAttribute(): string
    {
        return $this->name ?: $this->date->translatedFormat('l');
    }

    public function getFormattedTimeAttribute(): ?string
    {
        if (!$this->start_time) {
            return null;
        }
        
        $start = \Carbon\Carbon::parse($this->start_time)->format('H:i');
        
        if ($this->end_time) {
            $end = \Carbon\Carbon::parse($this->end_time)->format('H:i');
            return "{$start} - {$end}";
        }
        
        return $start;
    }

    // Checks
    public function isPast(): bool
    {
        return $this->date->isPast();
    }

    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    public function isFuture(): bool
    {
        return $this->date->isFuture();
    }
}
