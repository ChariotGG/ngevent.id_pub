<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['events' => fn($q) => $q->where('status', 'published')])
            ->get();

        $featuredEvents = Event::with(['organizer', 'category'])
            ->published()
            ->upcoming()
            ->where('is_featured', true)
            ->orderBy('start_date')
            ->limit(6)
            ->get();

        $upcomingEvents = Event::with(['organizer', 'category'])
            ->published()
            ->upcoming()
            ->orderBy('start_date')
            ->limit(8)
            ->get();

        return view('pages.home.index', compact('categories', 'featuredEvents', 'upcomingEvents'));
    }
}
