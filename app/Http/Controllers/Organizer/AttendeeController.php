<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\IssuedTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendeeController extends Controller
{
    public function index(): View
    {
        $organizer = auth()->user()->organizer;

        $events = Event::where('organizer_id', $organizer->id)
            ->where('status', 'published')
            ->withCount(['issuedTickets', 'issuedTickets as checked_in_count' => function ($query) {
                $query->where('is_used', true);
            }])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('organizer.attendees.index', compact('events'));
    }

    public function event(Event $event, Request $request): View
    {
        $organizer = auth()->user()->organizer;

        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $query = IssuedTicket::with(['orderItem.order', 'orderItem'])
            ->whereHas('orderItem.order', function ($q) use ($event) {
                $q->where('event_id', $event->id)
                  ->where('status', 'paid');
            });

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('attendee_name', 'like', '%' . $request->search . '%')
                  ->orWhere('attendee_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'checked_in') {
                $query->where('is_used', true);
            } else {
                $query->where('is_used', false);
            }
        }

        $attendees = $query->orderBy('attendee_name')->paginate(20);

        $stats = [
            'total' => IssuedTicket::whereHas('orderItem.order', function ($q) use ($event) {
                $q->where('event_id', $event->id)->where('status', 'paid');
            })->count(),
            'checked_in' => IssuedTicket::whereHas('orderItem.order', function ($q) use ($event) {
                $q->where('event_id', $event->id)->where('status', 'paid');
            })->where('is_used', true)->count(),
        ];

        return view('organizer.attendees.event', compact('event', 'attendees', 'stats'));
    }

    public function checkin(IssuedTicket $ticket): RedirectResponse
    {
        $organizer = auth()->user()->organizer;
        $event = $ticket->orderItem->order->event;

        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        if ($ticket->is_used) {
            return back()->with('error', 'Tiket sudah digunakan pada ' . $ticket->used_at->format('d M Y H:i'));
        }

        $ticket->update([
            'is_used' => true,
            'used_at' => now(),
            'used_by' => auth()->id(),
        ]);

        return back()->with('success', 'Check-in berhasil untuk ' . $ticket->attendee_name);
    }
}
