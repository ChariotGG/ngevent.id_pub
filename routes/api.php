<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Minimal untuk MVP)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.')->group(function () {

    // Public API - Categories
    Route::get('/categories', function () {
        return \App\Models\Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'color']);
    })->name('categories');

    // Public API - Events (Published Only)
    Route::get('/events', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Event::with(['category:id,name,slug', 'organizer:id,name,slug'])
            ->select(['id', 'organizer_id', 'category_id', 'title', 'slug', 'poster', 'start_date', 'city', 'min_price', 'max_price', 'is_free'])
            ->where('status', 'published')
            ->where('start_date', '>=', now());

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        return $query->orderBy('start_date')->paginate(12);
    })->name('events');

    // Cities (untuk autocomplete)
    Route::get('/cities', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->distinct()
            ->orderBy('city');

        if ($request->filled('q')) {
            $query->where('city', 'like', $request->q . '%');
        }

        return $query->limit(5)->pluck('city');
    })->name('cities');
});
