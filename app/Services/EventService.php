<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventDay;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventService
{
    public function __construct(
        private TicketService $ticketService,
    ) {}

    public function getPublishedEvents(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Event::query()
            ->with(['organizer', 'category', 'subcategories'])
            ->published()
            ->upcoming();

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('start_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['is_free'])) {
            $query->where('is_free', true);
        }

        $sortBy = $filters['sort'] ?? 'date';
        match ($sortBy) {
            'popular' => $query->orderByDesc('views_count'),
            'price_low' => $query->orderBy('min_price'),
            'price_high' => $query->orderByDesc('max_price'),
            default => $query->orderBy('start_date'),
        };

        return $query->paginate($perPage);
    }

    public function getFeaturedEvents(int $limit = 6): Collection
    {
        return Event::query()
            ->with(['organizer', 'category'])
            ->published()
            ->upcoming()
            ->featured()
            ->orderBy('start_date')
            ->limit($limit)
            ->get();
    }

    public function getEventForDisplay(string $slug): ?Event
    {
        $event = Event::query()
            ->with(['organizer.socialLinks', 'category', 'subcategories', 'days', 'tickets.activeVariants.eventDay'])
            ->where('slug', $slug)
            ->whereIn('status', EventStatus::publicStatuses())
            ->first();

        if ($event) {
            $event->incrementViews();
        }

        return $event;
    }

    public function create(Organizer $organizer, array $data): Event
    {
        return DB::transaction(function () use ($organizer, $data) {
            $posterPath = isset($data['poster']) ? $data['poster']->store('events/posters', 'public') : null;
            $bannerPath = isset($data['banner']) ? $data['banner']->store('events/banners', 'public') : null;
            $proposalPath = isset($data['proposal_file']) ? $data['proposal_file']->store('events/proposals', 'public') : null;

            $event = Event::create([
                'organizer_id' => $organizer->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'poster' => $posterPath,
                'banner' => $bannerPath,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'venue_name' => $data['venue_name'] ?? null,
                'venue_address' => $data['venue_address'] ?? null,
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
                'is_online' => $data['is_online'] ?? false,
                'online_url' => $data['online_url'] ?? null,
                'is_free' => $data['is_free'] ?? false,
                'proposal_file' => $proposalPath,
                'status' => EventStatus::DRAFT,
            ]);

            if (!empty($data['subcategories'])) {
                $event->subcategories()->sync($data['subcategories']);
            }

            $this->createEventDays($event, $data);

            return $event;
        });
    }

    public function update(Event $event, array $data): Event
    {
        return DB::transaction(function () use ($event, $data) {
            $updateData = collect($data)->only([
                'category_id', 'title', 'description', 'short_description',
                'start_date', 'end_date', 'start_time', 'end_time',
                'venue_name', 'venue_address', 'city', 'province',
                'is_online', 'online_url', 'is_free',
            ])->toArray();

            if (isset($data['poster'])) {
                if ($event->poster) Storage::disk('public')->delete($event->poster);
                $updateData['poster'] = $data['poster']->store('events/posters', 'public');
            }

            if (isset($data['banner'])) {
                if ($event->banner) Storage::disk('public')->delete($event->banner);
                $updateData['banner'] = $data['banner']->store('events/banners', 'public');
            }

            $event->update($updateData);

            if (isset($data['subcategories'])) {
                $event->subcategories()->sync($data['subcategories']);
            }

            return $event->fresh();
        });
    }

    public function submitForReview(Event $event): bool
    {
        if ($event->status !== EventStatus::DRAFT) return false;
        return $event->update(['status' => EventStatus::PENDING_REVIEW]);
    }

    public function approve(Event $event, User $admin, ?string $notes = null): bool
    {
        if ($event->status !== EventStatus::PENDING_REVIEW) return false;
        return $event->update([
            'status' => EventStatus::APPROVED,
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'admin_notes' => $notes,
        ]);
    }

    public function reject(Event $event, User $admin, string $reason): bool
    {
        if ($event->status !== EventStatus::PENDING_REVIEW) return false;
        return $event->update([
            'status' => EventStatus::DRAFT,
            'rejection_reason' => $reason,
        ]);
    }

    public function publish(Event $event): bool
    {
        if ($event->status !== EventStatus::APPROVED) return false;
        $event->updatePriceRange();
        return $event->update(['status' => EventStatus::PUBLISHED, 'published_at' => now()]);
    }

    public function unpublish(Event $event): bool
    {
        if ($event->status !== EventStatus::PUBLISHED) return false;
        return $event->update(['status' => EventStatus::APPROVED, 'published_at' => null]);
    }

    public function cancel(Event $event, string $reason): bool
    {
        if (in_array($event->status, [EventStatus::CANCELLED, EventStatus::COMPLETED])) return false;
        return $event->update(['status' => EventStatus::CANCELLED, 'admin_notes' => "Cancelled: {$reason}"]);
    }

    public function complete(Event $event): bool
    {
        if ($event->status !== EventStatus::PUBLISHED) return false;
        return $event->update(['status' => EventStatus::COMPLETED]);
    }

    protected function createEventDays(Event $event, array $data): void
    {
        if (!$event->isMultiDay()) {
            EventDay::create([
                'event_id' => $event->id,
                'date' => $event->start_date,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
            ]);
            return;
        }

        if (!empty($data['days'])) {
            foreach ($data['days'] as $dayData) {
                EventDay::create([
                    'event_id' => $event->id,
                    'date' => $dayData['date'],
                    'name' => $dayData['name'] ?? null,
                    'start_time' => $dayData['start_time'] ?? null,
                    'end_time' => $dayData['end_time'] ?? null,
                ]);
            }
        } else {
            $currentDate = $event->start_date->copy();
            $dayNumber = 1;
            while ($currentDate->lte($event->end_date)) {
                EventDay::create([
                    'event_id' => $event->id,
                    'date' => $currentDate->toDateString(),
                    'name' => "Day {$dayNumber}",
                ]);
                $currentDate->addDay();
                $dayNumber++;
            }
        }
    }

    public function getOrganizerEvents(Organizer $organizer, array $filters = []): LengthAwarePaginator
    {
        $query = Event::where('organizer_id', $organizer->id)->with(['category']);
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['search'])) $query->search($filters['search']);
        return $query->latest()->paginate(10);
    }

    public function getPendingReviewEvents(): Collection
    {
        return Event::with(['organizer', 'category'])
            ->where('status', EventStatus::PENDING_REVIEW)
            ->oldest()
            ->get();
    }

    public function getEventCities(): Collection
    {
        return Event::published()->upcoming()
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city');
    }
}
