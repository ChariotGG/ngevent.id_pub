<?php

namespace App\Services;

use App\Enums\TicketType;
use App\Exceptions\InsufficientTicketException;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function create(Event $event, array $data): Ticket
    {
        return DB::transaction(function () use ($event, $data) {
            $ticket = Ticket::create([
                'event_id' => $event->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? TicketType::REGULAR,
                'benefits' => $data['benefits'] ?? null,
                'max_per_order' => $data['max_per_order'] ?? 10,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($ticket, $variantData);
                }
            }

            $event->updatePriceRange();

            return $ticket;
        });
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update(collect($data)->only([
            'name', 'description', 'type', 'benefits', 'max_per_order', 'sort_order', 'is_active'
        ])->toArray());

        $ticket->event->updatePriceRange();

        return $ticket->fresh();
    }

    public function delete(Ticket $ticket): bool
    {
        if ($ticket->orderItems()->exists()) {
            return false;
        }

        $event = $ticket->event;
        $ticket->delete();
        $event->updatePriceRange();

        return true;
    }

    public function createVariant(Ticket $ticket, array $data): TicketVariant
    {
        $variant = TicketVariant::create([
            'ticket_id' => $ticket->id,
            'event_day_id' => $data['event_day_id'] ?? null,
            'name' => $data['name'] ?? null,
            'price' => $data['price'] ?? 0,
            'original_price' => $data['original_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'min_per_order' => $data['min_per_order'] ?? 1,
            'max_per_order' => $data['max_per_order'] ?? null,
            'sale_start_at' => $data['sale_start_at'] ?? null,
            'sale_end_at' => $data['sale_end_at'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $ticket->event->updatePriceRange();

        return $variant;
    }

    public function updateVariant(TicketVariant $variant, array $data): TicketVariant
    {
        $variant->update(collect($data)->only([
            'event_day_id', 'name', 'price', 'original_price', 'stock',
            'min_per_order', 'max_per_order', 'sale_start_at', 'sale_end_at', 'is_active'
        ])->toArray());

        $variant->ticket->event->updatePriceRange();

        return $variant->fresh();
    }

    public function deleteVariant(TicketVariant $variant): bool
    {
        if ($variant->orderItems()->exists()) {
            return false;
        }

        $event = $variant->ticket->event;
        $variant->delete();
        $event->updatePriceRange();

        return true;
    }

    public function getAvailableVariants(Event $event): Collection
    {
        return TicketVariant::query()
            ->whereHas('ticket', fn($q) => $q->where('event_id', $event->id)->where('is_active', true))
            ->available()
            ->with(['ticket', 'eventDay'])
            ->get();
    }

    public function checkAvailability(int $variantId, int $quantity): bool
    {
        $variant = TicketVariant::find($variantId);

        if (!$variant || !$variant->isAvailable()) {
            return false;
        }

        return $variant->available_stock >= $quantity;
    }

    public function reserveStock(int $variantId, int $quantity): void
    {
        $variant = TicketVariant::lockForUpdate()->findOrFail($variantId);

        if ($variant->available_stock < $quantity) {
            throw new InsufficientTicketException(
                $variantId,
                $quantity,
                $variant->available_stock
            );
        }

        $variant->increment('reserved_count', $quantity);
    }

    public function releaseStock(int $variantId, int $quantity): void
    {
        $variant = TicketVariant::lockForUpdate()->findOrFail($variantId);
        $variant->decrement('reserved_count', min($quantity, $variant->reserved_count));
    }

    public function confirmSale(int $variantId, int $quantity): void
    {
        $variant = TicketVariant::lockForUpdate()->findOrFail($variantId);
        $variant->decrement('reserved_count', $quantity);
        $variant->increment('sold_count', $quantity);
    }

    public function returnStock(int $variantId, int $quantity): void
    {
        $variant = TicketVariant::lockForUpdate()->findOrFail($variantId);
        $variant->decrement('sold_count', min($quantity, $variant->sold_count));
    }

    public function getTicketsForEvent(Event $event): Collection
    {
        return $event->tickets()
            ->with(['variants.eventDay'])
            ->orderBy('sort_order')
            ->get();
    }

    public function getTicketStats(Event $event): array
    {
        $tickets = $event->tickets()->with('variants')->get();

        $totalStock = 0;
        $totalSold = 0;
        $totalRevenue = 0;

        foreach ($tickets as $ticket) {
            foreach ($ticket->variants as $variant) {
                $totalStock += $variant->stock;
                $totalSold += $variant->sold_count;
                $totalRevenue += $variant->sold_count * $variant->price;
            }
        }

        return [
            'total_stock' => $totalStock,
            'total_sold' => $totalSold,
            'total_available' => $totalStock - $totalSold,
            'total_revenue' => $totalRevenue,
            'sell_through_rate' => $totalStock > 0 ? round(($totalSold / $totalStock) * 100, 1) : 0,
        ];
    }
}
