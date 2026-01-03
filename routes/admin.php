<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Placeholder routes
        Route::view('events', 'admin.placeholder', ['title' => 'Events'])->name('events.index');
        Route::view('categories', 'admin.placeholder', ['title' => 'Categories'])->name('categories.index');
        Route::view('users', 'admin.placeholder', ['title' => 'Users'])->name('users.index');
        Route::view('organizers', 'admin.placeholder', ['title' => 'Organizers'])->name('organizers.index');
        Route::view('vouchers', 'admin.placeholder', ['title' => 'Vouchers'])->name('vouchers.index');
        Route::view('settlements', 'admin.placeholder', ['title' => 'Settlements'])->name('settlements.index');
    });
