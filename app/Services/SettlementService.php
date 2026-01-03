<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\SettlementStatus;
use App\Events\SettlementProcessed;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettlementService
{
    public function createForEvent(Event $event): Settlement
    {
        $organizer = $event->organizer;
        $calculation = $this->calculate($event);

        return Settlement::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'gross_amount' => $calculation['gross_amount'],
            'platform_fee' => $calculation['platform_fee'],
            'payment_fee_total' => $calculation['payment_fee_total'],
            'refund_amount' => $calculation['refund_amount'],
            'net_amount' => $calculation['net_amount'],
            'bank_name' => $organizer->bank_name,
            'bank_account_number' => $organizer->bank_account_number,
            'bank_account_name' => $organizer->bank_account_name,
            'status' => SettlementStatus::PENDING,
        ]);
    }

    public function calculate(Event $event): array
    {
        $paidOrders = $event->orders()
            ->whereIn('status', [OrderStatus::PAID, OrderStatus::COMPLETED])
            ->get();

        $refundedOrders = $event->orders()
            ->where('status', OrderStatus::REFUNDED)
            ->get();

        $grossAmount = $paidOrders->sum('subtotal') - $paidOrders->sum('discount');
        $platformFee = $paidOrders->sum('platform_fee');
        $paymentFeeTotal = $paidOrders->sum('payment_fee');
        $refundAmount = $refundedOrders->sum('subtotal') - $refundedOrders->sum('discount');
        $netAmount = $grossAmount - $platformFee - $refundAmount;

        return [
            'gross_amount' => max(0, $grossAmount),
            'platform_fee' => max(0, $platformFee),
            'payment_fee_total' => max(0, $paymentFeeTotal),
            'refund_amount' => max(0, $refundAmount),
            'net_amount' => max(0, $netAmount),
            'total_orders' => $paidOrders->count(),
            'total_tickets' => $paidOrders->sum(fn($o) => $o->items->sum('quantity')),
        ];
    }

    public function process(Settlement $settlement, User $admin): Settlement
    {
        if (!$settlement->canProcess()) {
            return $settlement;
        }

        $settlement->update([
            'status' => SettlementStatus::PROCESSING,
            'processed_by' => $admin->id,
        ]);

        return $settlement->fresh();
    }

    public function markAsTransferred(
        Settlement $settlement,
        string $reference,
        ?string $proofPath = null,
        ?string $notes = null
    ): Settlement {
        return DB::transaction(function () use ($settlement, $reference, $proofPath, $notes) {
            $settlement->update([
                'status' => SettlementStatus::TRANSFERRED,
                'transfer_reference' => $reference,
                'transfer_proof' => $proofPath,
                'transferred_at' => now(),
                'notes' => $notes,
            ]);

            event(new SettlementProcessed($settlement));

            Log::channel('payment')->info('Settlement transferred', [
                'settlement_id' => $settlement->id,
                'event_id' => $settlement->event_id,
                'amount' => $settlement->net_amount,
                'reference' => $reference,
            ]);

            return $settlement->fresh();
        });
    }

    public function markAsFailed(Settlement $settlement, string $reason): Settlement
    {
        $settlement->update([
            'status' => SettlementStatus::FAILED,
            'admin_notes' => $reason,
        ]);

        return $settlement->fresh();
    }

    public function retry(Settlement $settlement, User $admin): Settlement
    {
        if (!$settlement->canRetry()) {
            return $settlement;
        }

        $settlement->update([
            'status' => SettlementStatus::PENDING,
            'admin_notes' => null,
        ]);

        return $this->process($settlement, $admin);
    }

    public function getPendingSettlements(): Collection
    {
        return Settlement::query()
            ->with(['event', 'organizer'])
            ->where('status', SettlementStatus::PENDING)
            ->orderBy('created_at')
            ->get();
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Settlement::query()->with(['event', 'organizer']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['organizer_id'])) {
            $query->where('organizer_id', $filters['organizer_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('event', fn($q) => $q->where('title', 'ilike', "%{$search}%"))
                ->orWhereHas('organizer', fn($q) => $q->where('name', 'ilike', "%{$search}%"));
        }

        return $query->latest()->paginate(20);
    }

    public function getOrganizerSettlements(Organizer $organizer): LengthAwarePaginator
    {
        return Settlement::query()
            ->with(['event'])
            ->where('organizer_id', $organizer->id)
            ->latest()
            ->paginate(10);
    }

    public function getEventsReadyForSettlement(): Collection
    {
        $delayDays = config('ngevent.settlement_delay_days', 7);

        return Event::query()
            ->with(['organizer'])
            ->where('status', EventStatus::COMPLETED)
            ->where('end_date', '<=', now()->subDays($delayDays))
            ->whereDoesntHave('settlement')
            ->whereHas('orders', fn($q) => $q->whereIn('status', [OrderStatus::PAID, OrderStatus::COMPLETED]))
            ->get();
    }

    public function processReadySettlements(): int
    {
        $events = $this->getEventsReadyForSettlement();
        $count = 0;

        foreach ($events as $event) {
            try {
                $this->createForEvent($event);
                $count++;

                Log::channel('payment')->info('Settlement created for event', [
                    'event_id' => $event->id,
                    'event_title' => $event->title,
                ]);
            } catch (\Exception $e) {
                Log::channel('payment')->error('Failed to create settlement', [
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    public function getSettlementStats(): array
    {
        return [
            'pending_count' => Settlement::where('status', SettlementStatus::PENDING)->count(),
            'pending_amount' => Settlement::where('status', SettlementStatus::PENDING)->sum('net_amount'),
            'processing_count' => Settlement::where('status', SettlementStatus::PROCESSING)->count(),
            'transferred_this_month' => Settlement::where('status', SettlementStatus::TRANSFERRED)
                ->whereMonth('transferred_at', now()->month)
                ->sum('net_amount'),
        ];
    }
}
