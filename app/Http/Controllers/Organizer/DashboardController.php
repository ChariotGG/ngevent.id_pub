<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $organizer = auth()->user()->organizer;

        if (!$organizer) {
            abort(403, 'Anda belum terdaftar sebagai organizer');
        }

        $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');

        // Statistics
        $stats = [
            'total_events' => Event::where('organizer_id', $organizer->id)->count(),
            'published_events' => Event::where('organizer_id', $organizer->id)->where('status', 'published')->count(),
            'total_orders' => Order::whereIn('event_id', $eventIds)->count(),
            'total_revenue' => Order::whereIn('event_id', $eventIds)->where('status', OrderStatus::PAID)->sum('subtotal'),
            'total_tickets_sold' => Order::whereIn('event_id', $eventIds)->where('status', OrderStatus::PAID)->withSum('items', 'quantity')->get()->sum('items_sum_quantity'),
        ];

        // Recent Orders
        $recentOrders = Order::with(['event', 'user'])
            ->whereIn('event_id', $eventIds)
            ->latest()
            ->take(5)
            ->get();

        // Upcoming Events
        $upcomingEvents = Event::where('organizer_id', $organizer->id)
            ->where('start_date', '>=', now())
            ->where('status', 'published')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Featured Events (event yang di-highlight/featured)
        $featuredEvents = Event::where('organizer_id', $organizer->id)
            ->where('status', 'published')
            ->where('is_featured', true) // Jika ada kolom is_featured
            ->orWhere(function($query) use ($organizer) {
                // Atau ambil events dengan penjualan terbanyak
                $query->where('organizer_id', $organizer->id)
                      ->where('status', 'published')
                      ->orderBy('created_at', 'desc');
            })
            ->take(3)
            ->get();

        // Categories
        $categories = Category::active()
            ->ordered()
            ->withCount(['events' => function ($query) {
                $query->where('status', 'published');
            }])
            ->get();

        return view('organizer.dashboard', compact(
            'stats',
            'recentOrders',
            'upcomingEvents',
            'featuredEvents', // ← TAMBAHKAN INI
            'organizer',
            'categories'
        ));
    }
}
