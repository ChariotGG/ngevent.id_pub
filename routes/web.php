<?php

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\CheckoutController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Events (Public)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/category/{category:slug}', [EventController::class, 'category'])->name('events.category');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Organizer Profile (Public)
Route::get('/organizer/{organizer:slug}', [EventController::class, 'organizer'])->name('organizer.show');

// Checkout (GUEST ALLOWED - NO AUTH REQUIRED)
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/{event:slug}', [CheckoutController::class, 'index'])->name('index');
    Route::post('/{event:slug}', [CheckoutController::class, 'store'])->name('store');
    Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('payment');
    Route::post('/payment/{order}', [CheckoutController::class, 'processPayment'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    Route::get('/failed/{order}', [CheckoutController::class, 'failed'])->name('failed');
    Route::get('/expired/{order}', [CheckoutController::class, 'expired'])->name('expired');
});

// Order Lookup (Guest - via email verification)
Route::get('/my-tickets', function () {
    return view('pages.tickets.lookup');
})->name('tickets.lookup');

Route::post('/my-tickets/verify', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'order_number' => 'required|string',
    ]);

    $order = \App\Models\Order::where('order_number', $request->order_number)
        ->where('customer_email', $request->email)
        ->with(['event', 'issuedTickets'])
        ->first();

    if (!$order) {
        return back()->withErrors(['email' => 'Order tidak ditemukan dengan email dan nomor order tersebut']);
    }

    return view('pages.tickets.show', compact('order'));
})->name('tickets.verify');
