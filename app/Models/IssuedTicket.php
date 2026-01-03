<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class IssuedTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'user_id',
        'code',
        'qr_code',
        'attendee_name',
        'attendee_email',
        'attendee_phone',
        'is_used',
        'used_at',
        'used_by',
        'check_in_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    // Relationships
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    // Accessors
    public function getEventAttribute(): ?Event
    {
        return $this->orderItem?->order?->event;
    }

    public function getTicketAttribute(): ?Ticket
    {
        return $this->orderItem?->ticket;
    }

    public function getTicketVariantAttribute(): ?TicketVariant
    {
        return $this->orderItem?->ticketVariant;
    }

    public function getQrCodeImageAttribute(): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(
            QrCode::format('svg')
                ->size(200)
                ->errorCorrection('H')
                ->generate($this->code)
        );
    }

    public function getStatusAttribute(): string
    {
        return $this->is_used ? 'Sudah Digunakan' : 'Belum Digunakan';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_used ? 'gray' : 'green';
    }

    // Methods
    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (static::where('code', $code)->exists());
        
        return $code;
    }

    public function markAsUsed(?int $usedBy = null, ?string $notes = null): bool
    {
        if ($this->is_used) {
            return false;
        }

        return $this->update([
            'is_used' => true,
            'used_at' => now(),
            'used_by' => $usedBy,
            'check_in_notes' => $notes,
        ]);
    }

    public function resetUsage(): bool
    {
        return $this->update([
            'is_used' => false,
            'used_at' => null,
            'used_by' => null,
            'check_in_notes' => null,
        ]);
    }

    // Scopes
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->whereHas('orderItem.order', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        });
    }

    // Boot
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (!$ticket->code) {
                $ticket->code = static::generateCode();
            }
        });
    }
}
