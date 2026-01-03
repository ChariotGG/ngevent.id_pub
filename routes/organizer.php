<?php

use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\OrderController;
use App\Http\Controllers\Organizer\AttendeeController;
use App\Http\Controllers\Organizer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'organizer'])
    ->prefix('organizer')
    ->name('organizer.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Events
        Route::resource('events', EventController::class);
        Route::post('events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
        Route::post('events/{event}/unpublish', [EventController::class, 'unpublish'])->name('events.unpublish');
        Route::post('events/{event}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Attendees / Check-in
        Route::get('attendees', [AttendeeController::class, 'index'])->name('attendees.index');
        Route::get('attendees/{event}', [AttendeeController::class, 'event'])->name('attendees.event');
        Route::post('attendees/{ticket}/checkin', [AttendeeController::class, 'checkin'])->name('attendees.checkin');

        // Settings
        Route::get('settings', [ProfileController::class, 'index'])->name('settings.index');
        Route::put('settings', [ProfileController::class, 'update'])->name('settings.update');
    });
