<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizerSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'platform',
        'url',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function getIconAttribute(): string
    {
        return match ($this->platform) {
            'instagram' => 'instagram',
            'twitter' => 'twitter',
            'facebook' => 'facebook',
            'youtube' => 'youtube',
            'tiktok' => 'tiktok',
            'website' => 'globe',
            default => 'link',
        };
    }

    public function getColorAttribute(): string
    {
        return match ($this->platform) {
            'instagram' => '#E4405F',
            'twitter' => '#1DA1F2',
            'facebook' => '#1877F2',
            'youtube' => '#FF0000',
            'tiktok' => '#000000',
            default => '#6B7280',
        };
    }
}
