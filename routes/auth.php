<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Register (Organizer Only - no regular user registration)
    Route::get('/register/organizer', [RegisterController::class, 'showOrganizerForm'])->name('register.organizer');
    Route::post('/register/organizer', [RegisterController::class, 'registerOrganizer'])->name('register');

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Email Verification Routes
    // ← TAMBAHKAN: Halaman "Please verify your email"
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Route untuk verifikasi email (ketika user klik link di email)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        // Clear cache & regenerate session
        $request->session()->regenerate();

        return redirect()->route('organizer.dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selamat datang di ngevent.id 🎉');
    })->middleware(['signed'])->name('verification.verify');

    // Route untuk resend verification email
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi telah dikirim ke email Anda!');
    })->middleware(['throttle:3,1'])->name('verification.send');
});
