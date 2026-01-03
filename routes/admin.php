<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');

        // Placeholder routes - akan diganti dengan controllers nanti
        Route::get('/events', function () {
            return view('admin.dashboard.index');
        })->name('events.index');

        Route::get('/categories', function () {
            return view('admin.dashboard.index');
        })->name('categories.index');

        Route::get('/users', function () {
            return view('admin.dashboard.index');
        })->name('users.index');

        Route::get('/organizers', function () {
            return view('admin.dashboard.index');
        })->name('organizers.index');

        Route::get('/vouchers', function () {
            return view('admin.dashboard.index');
        })->name('vouchers.index');

        Route::get('/settlements', function () {
            return view('admin.dashboard.index');
        })->name('settlements.index');
    });
