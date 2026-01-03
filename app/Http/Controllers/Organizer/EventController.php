<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = auth()->user()->organizer;

        $query = Event::where('organizer_id', $organizer->id)
            ->withCount(['orders', 'tickets']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->latest()->paginate(10);

        return view('organizer.events.index', compact('events'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'is_online' => 'boolean',
            'online_url' => 'nullable|url|required_if:is_online,true',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.description' => 'nullable|string',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.stock' => 'required|integer|min:1',
        ]);

        $organizer = auth()->user()->organizer;

        try {
            DB::beginTransaction();

            // Create event
            $event = Event::create([
                'organizer_id' => $organizer->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . Str::random(6),
                'description' => $request->description,
                'short_description' => $request->short_description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'timezone' => 'Asia/Jakarta',
                'venue_name' => $request->venue_name,
                'venue_address' => $request->venue_address,
                'city' => $request->city,
                'province' => $request->province,
                'is_online' => $request->boolean('is_online'),
                'online_url' => $request->online_url,
                'status' => 'draft',
                'is_free' => collect($request->tickets)->every(fn($t) => $t['price'] == 0),
            ]);

            // Create tickets
            $minPrice = null;
            $maxPrice = null;

            foreach ($request->tickets as $ticketData) {
                $ticket = $event->tickets()->create([
                    'name' => $ticketData['name'],
                    'description' => $ticketData['description'] ?? null,
                    'type' => $ticketData['price'] == 0 ? 'free' : 'paid',
                    'is_active' => true,
                ]);

                $ticket->variants()->create([
                    'name' => null,
                    'price' => $ticketData['price'],
                    'stock' => $ticketData['stock'],
                    'is_active' => true,
                ]);

                if ($minPrice === null || $ticketData['price'] < $minPrice) {
                    $minPrice = $ticketData['price'];
                }
                if ($maxPrice === null || $ticketData['price'] > $maxPrice) {
                    $maxPrice = $ticketData['price'];
                }
            }

            $event->update([
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ]);

            // Create event days
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $dayNumber = 1;

            while ($startDate <= $endDate) {
                $event->days()->create([
                    'date' => $startDate->format('Y-m-d'),
                    'day_number' => $dayNumber,
                    'name' => 'Hari ' . $dayNumber,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);
                $startDate->addDay();
                $dayNumber++;
            }

            DB::commit();

            return redirect()->route('organizer.events.show', $event)
                ->with('success', 'Event berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat event: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Event $event): View
    {
        $this->authorizeEvent($event);

        $event->load(['category', 'tickets.variants', 'days']);

        $stats = [
            'total_orders' => $event->orders()->count(),
            'paid_orders' => $event->orders()->where('status', OrderStatus::PAID)->count(),
            'total_revenue' => $event->orders()->where('status', OrderStatus::PAID)->sum('subtotal'),
            'tickets_sold' => $event->orders()->where('status', OrderStatus::PAID)->withSum('items', 'quantity')->get()->sum('items_sum_quantity'),
        ];

        return view('organizer.events.show', compact('event', 'stats'));
    }

    public function edit(Event $event): View
    {
        $this->authorizeEvent($event);

        $categories = Category::orderBy('name')->get();
        $event->load(['tickets.variants']);

        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
        ]);

        $event->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'venue_name' => $request->venue_name,
            'venue_address' => $request->venue_address,
            'city' => $request->city,
            'province' => $request->province,
            'is_online' => $request->boolean('is_online'),
            'online_url' => $request->online_url,
        ]);

        return redirect()->route('organizer.events.show', $event)
            ->with('success', 'Event berhasil diperbarui');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        if ($event->orders()->exists()) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah ada pesanan');
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil dihapus');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $event->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Event berhasil dipublikasikan');
    }

    public function unpublish(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $event->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return back()->with('success', 'Event berhasil di-unpublish');
    }

    public function duplicate(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->slug = Str::slug($newEvent->title) . '-' . Str::random(6);
        $newEvent->status = 'draft';
        $newEvent->published_at = null;
        $newEvent->views_count = 0;
        $newEvent->save();

        // Duplicate tickets
        foreach ($event->tickets as $ticket) {
            $newTicket = $ticket->replicate();
            $newTicket->event_id = $newEvent->id;
            $newTicket->save();

            foreach ($ticket->variants as $variant) {
                $newVariant = $variant->replicate();
                $newVariant->ticket_id = $newTicket->id;
                $newVariant->sold_count = 0;
                $newVariant->reserved_count = 0;
                $newVariant->save();
            }
        }

        return redirect()->route('organizer.events.edit', $newEvent)
            ->with('success', 'Event berhasil diduplikasi');
    }

    protected function authorizeEvent(Event $event): void
    {
        $organizer = auth()->user()->organizer;

        if (!$organizer || $event->organizer_id !== $organizer->id) {
            abort(403);
        }
    }
}
