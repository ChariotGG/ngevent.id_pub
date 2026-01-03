<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::with(['organizer', 'category'])
            ->published()
            ->upcoming();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $events = $query->orderBy('start_date')->paginate(12);
        $categories = Category::active()->ordered()->get();

        return view('pages.events.index', compact('events', 'categories'));
    }

    public function show(Event $event): View
    {
        if (!$event->isPublished()) {
            abort(404);
        }

        $event->load(['organizer.socialLinks', 'category', 'subcategories', 'days', 'tickets.variants']);
        $event->incrementViews();

        $relatedEvents = Event::with(['organizer', 'category'])
            ->published()
            ->upcoming()
            ->where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->limit(4)
            ->get();

        return view('pages.events.show', compact('event', 'relatedEvents'));
    }

    public function category(Category $category): View
    {
        $events = Event::with(['organizer', 'category'])
            ->published()
            ->upcoming()
            ->where('category_id', $category->id)
            ->orderBy('start_date')
            ->paginate(12);

        return view('pages.events.category', compact('category', 'events'));
    }

    public function organizer(Organizer $organizer): View
    {
        $events = Event::with(['category'])
            ->published()
            ->where('organizer_id', $organizer->id)
            ->orderBy('start_date', 'desc')
            ->paginate(12);

        return view('pages.organizers.show', compact('organizer', 'events'));
    }
}
