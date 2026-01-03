<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.')->group(function () {

    // Public API - Categories
    Route::get('/categories', function () {
        return \App\Models\Category::active()->ordered()->get();
    })->name('categories');

    // Public API - Events
    Route::get('/events', function () {
        return \App\Models\Event::with(['category', 'organizer'])
            ->published()
            ->upcoming()
            ->paginate(12);
    })->name('events');

    Route::get('/events/featured', function () {
        return \App\Models\Event::with(['category', 'organizer'])
            ->published()
            ->upcoming()
            ->featured()
            ->limit(6)
            ->get();
    })->name('events.featured');

    // TODO: Add full API controllers later
});
