<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IssuedTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = IssuedTicket::with(['orderItem.order.event', 'orderItem.ticket'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pages.tickets.index', compact('tickets'));
    }

    public function show(IssuedTicket $ticket): View
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['orderItem.order.event', 'orderItem.ticket', 'orderItem.ticketVariant']);

        return view('pages.tickets.show', compact('ticket'));
    }

    public function download(IssuedTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // TODO: Generate PDF
        return response()->download(storage_path('app/tickets/' . $ticket->code . '.pdf'));
    }
}
