<?php

namespace App\Services;

use App\Events\TicketIssued;
use App\Models\IssuedTicket;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ETicketService
{
    public function generateTickets(Order $order): Collection
    {
        return DB::transaction(function () use ($order) {
            $issuedTickets = collect();

            foreach ($order->items as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    $ticket = $this->createIssuedTicket($item, $order->user);
                    $issuedTickets->push($ticket);
                }
            }

            event(new TicketIssued($order, $issuedTickets));

            return $issuedTickets;
        });
    }

    protected function createIssuedTicket(OrderItem $item, User $user): IssuedTicket
    {
        return IssuedTicket::create([
            'order_item_id' => $item->id,
            'user_id' => $user->id,
            'code' => IssuedTicket::generateCode(),
            'attendee_name' => $item->order->customer_name,
            'attendee_email' => $item->order->customer_email,
            'attendee_phone' => $item->order->customer_phone,
        ]);
    }

    public function getTicketByCode(string $code): ?IssuedTicket
    {
        return IssuedTicket::with([
            'orderItem.order.event',
            'orderItem.ticket',
            'orderItem.ticketVariant.eventDay',
        ])->where('code', $code)->first();
    }

    public function checkIn(IssuedTicket $ticket, ?User $checkedInBy = null, ?string $notes = null): bool
    {
        if ($ticket->is_used) {
            return false;
        }

        return $ticket->markAsUsed($checkedInBy?->id, $notes);
    }

    public function undoCheckIn(IssuedTicket $ticket): bool
    {
        if (!$ticket->is_used) {
            return false;
        }

        return $ticket->resetUsage();
    }

    public function validateTicket(string $code, int $eventId): array
    {
        $ticket = $this->getTicketByCode($code);

        if (!$ticket) {
            return [
                'valid' => false,
                'message' => 'Tiket tidak ditemukan',
                'ticket' => null,
            ];
        }

        $order = $ticket->orderItem->order;

        if ($order->event_id !== $eventId) {
            return [
                'valid' => false,
                'message' => 'Tiket bukan untuk event ini',
                'ticket' => null,
            ];
        }

        if (!$order->isPaid()) {
            return [
                'valid' => false,
                'message' => 'Pembayaran belum selesai',
                'ticket' => null,
            ];
        }

        if ($ticket->is_used) {
            return [
                'valid' => false,
                'message' => 'Tiket sudah digunakan pada ' . $ticket->used_at->format('d M Y H:i'),
                'ticket' => $ticket,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Tiket valid',
            'ticket' => $ticket,
        ];
    }

    public function getUserTickets(User $user): Collection
    {
        return IssuedTicket::query()
            ->with([
                'orderItem.order.event',
                'orderItem.ticket',
                'orderItem.ticketVariant.eventDay',
            ])
            ->where('user_id', $user->id)
            ->whereHas('orderItem.order', fn($q) => $q->whereIn('status', ['paid', 'completed']))
            ->latest()
            ->get();
    }

    public function getEventAttendees(int $eventId, array $filters = []): Collection
    {
        $query = IssuedTicket::query()
            ->with(['orderItem.ticket', 'orderItem.ticketVariant.eventDay', 'user'])
            ->whereHas('orderItem.order', fn($q) => $q->where('event_id', $eventId)->whereIn('status', ['paid', 'completed']));

        if (isset($filters['is_used'])) {
            $query->where('is_used', $filters['is_used']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('attendee_name', 'ilike', "%{$search}%")
                    ->orWhere('attendee_email', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['ticket_id'])) {
            $query->whereHas('orderItem', fn($q) => $q->where('ticket_id', $filters['ticket_id']));
        }

        return $query->orderBy('created_at')->get();
    }

    public function getCheckInStats(int $eventId): array
    {
        $tickets = IssuedTicket::query()
            ->whereHas('orderItem.order', fn($q) => $q->where('event_id', $eventId)->whereIn('status', ['paid', 'completed']))
            ->get();

        $total = $tickets->count();
        $checkedIn = $tickets->where('is_used', true)->count();

        return [
            'total' => $total,
            'checked_in' => $checkedIn,
            'remaining' => $total - $checkedIn,
            'percentage' => $total > 0 ? round(($checkedIn / $total) * 100, 1) : 0,
        ];
    }

    public function bulkUpdateAttendeeInfo(array $ticketIds, array $data): int
    {
        return IssuedTicket::whereIn('id', $ticketIds)
            ->update(collect($data)->only(['attendee_name', 'attendee_email', 'attendee_phone'])->toArray());
    }
}
