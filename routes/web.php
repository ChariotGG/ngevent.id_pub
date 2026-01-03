<?php

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/category/{category:slug}', [EventController::class, 'category'])->name('events.category');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Organizer Profile (public)
Route::get('/organizer/{organizer:slug}', [EventController::class, 'organizer'])->name('organizer.show');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout/{event:slug}', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/{event:slug}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/payment/{order}', [CheckoutController::class, 'processPayment'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed/{order}', [CheckoutController::class, 'failed'])->name('checkout.failed');
    Route::get('/checkout/expired/{order}', [CheckoutController::class, 'expired'])->name('checkout.expired');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket:code}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket:code}/download', [TicketController::class, 'download'])->name('tickets.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
