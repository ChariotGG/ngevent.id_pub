<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IssuedTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = IssuedTicket::with(['orderItem.order.event', 'orderItem'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pages.tickets.index', compact('tickets'));
    }

    public function show(string $code): View
    {
        $ticket = IssuedTicket::with(['orderItem.order.event', 'orderItem'])
            ->where('code', $code)
            ->firstOrFail();

        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        return view('pages.tickets.show', compact('ticket'));
    }

    public function download(string $code)
    {
        $ticket = IssuedTicket::with(['orderItem.order.event', 'orderItem.order'])
            ->where('code', $code)
            ->firstOrFail();

        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // Generate QR Code tanpa imagick (tidak pakai QR untuk PDF, tampilkan code saja)
        $pdf = Pdf::loadView('pdf.ticket', compact('ticket'));

        $pdf->setPaper('a5', 'landscape');

        return $pdf->download("ticket-{$ticket->code}.pdf");
    }
}
