<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Organizer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('organizer')
    ->name('organizer.')
    ->middleware(['auth', 'organizer'])
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            return view('organizer.dashboard.index');
        })->name('dashboard');

        // Placeholder routes - akan diganti dengan controllers nanti
        Route::get('/events', function () {
            return view('organizer.dashboard.index');
        })->name('events.index');

        Route::get('/orders', function () {
            return view('organizer.dashboard.index');
        })->name('orders.index');

        Route::get('/attendees', function () {
            return view('organizer.dashboard.index');
        })->name('attendees.index');

        Route::get('/settlements', function () {
            return view('organizer.dashboard.index');
        })->name('settlements.index');

        Route::get('/profile', function () {
            return view('organizer.dashboard.index');
        })->name('profile.index');
    });
