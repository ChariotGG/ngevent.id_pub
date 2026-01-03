<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Organizer;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_organizers' => Organizer::count(),
            'total_events' => Event::count(),
            'total_orders' => Order::count(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
