<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\CustomVerifyEmail; // ← TAMBAHKAN INI
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    // ← TAMBAHKAN METHOD INI (Custom Email Verification)
    /**
     * Override default Laravel email verification
     * Send custom branded email for ngevent.id
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    // Relationships
    public function organizer(): HasOne
    {
        return $this->hasOne(Organizer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function issuedTickets(): HasMany
    {
        return $this->hasMany(IssuedTicket::class);
    }

    // Role Checks
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOrganizer(): bool
    {
        return $this->role === UserRole::ORGANIZER;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    // Accessors
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Update: Gunakan brand color ngevent.id (pink)
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=FF8FC7&color=000';
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }
}
