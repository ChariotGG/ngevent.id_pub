<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::USER,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/');
    }

    public function showOrganizerForm(): View
    {
        return view('auth.register-organizer');
    }

    public function registerOrganizer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organizer_name' => 'required|string|max:255',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::ORGANIZER,
        ]);

        // Create organizer profile
        $user->organizer()->create([
            'name' => $request->organizer_name,
        ]);

        // Trigger email verification event
        event(new Registered($user));

        // Login user
        Auth::login($user);

        // Redirect to verification notice page
        return redirect()->route('verification.notice')
            ->with('success', 'Akun berhasil dibuat! Silakan cek email kamu untuk verifikasi.');
    }
}
