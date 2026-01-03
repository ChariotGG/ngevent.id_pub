<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin ngevent',
            'email' => 'admin@ngevent.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);

        // Organizer Users
        User::create([
            'name' => 'Event Organizer Demo',
            'email' => 'organizer@ngevent.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ORGANIZER,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Music Festival ID',
            'email' => 'music@ngevent.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ORGANIZER,
            'email_verified_at' => now(),
        ]);

        // Regular Users
        User::create([
            'name' => 'John Doe',
            'email' => 'user@ngevent.id',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'email_verified_at' => now(),
        ]);

        // Create more random users for testing
        User::factory(20)->create();
    }
}
