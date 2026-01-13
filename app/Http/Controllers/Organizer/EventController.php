<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketVariant;
use App\Models\EventDay;
use App\Enums\EventStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

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
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:100',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_online' => 'boolean',
            'online_url' => 'nullable|url|required_if:is_online,true',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.stock' => 'required|integer|min:1',
        ], [
            'title.required' => 'Judul event wajib diisi',
            'description.min' => 'Deskripsi minimal 100 karakter',
            'start_date.after' => 'Tanggal mulai harus setelah hari ini',
            'tickets.required' => 'Minimal 1 jenis tiket harus dibuat',
            'tickets.min' => 'Minimal 1 jenis tiket harus dibuat',
        ]);

        $organizer = auth()->user()->organizer;

        try {
            DB::beginTransaction();

            // Upload poster
            $posterPath = null;
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('events/posters', 'public');
            }

            // Create event
            $event = Event::create([
                'organizer_id' => $organizer->id,
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'timezone' => 'Asia/Jakarta',
                'venue_name' => $validated['venue_name'],
                'venue_address' => $validated['venue_address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'poster' => $posterPath,
                'is_online' => $request->boolean('is_online'),
                'online_url' => $validated['online_url'] ?? null,
                'status' => EventStatus::DRAFT,
                'is_free' => collect($validated['tickets'])->every(fn($t) => $t['price'] == 0),
            ]);

            // Create tickets
            $minPrice = null;
            $maxPrice = null;

            foreach ($validated['tickets'] as $index => $ticketData) {
                $ticket = $event->tickets()->create([
                    'name' => $ticketData['name'],
                    'type' => $ticketData['price'] == 0 ? 'free' : 'regular',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);

                $ticket->variants()->create([
                    'name' => null,
                    'price' => $ticketData['price'],
                    'stock' => $ticketData['stock'],
                    'is_active' => true,
                ]);

                $price = (int) $ticketData['price'];
                if ($minPrice === null || $price < $minPrice) $minPrice = $price;
                if ($maxPrice === null || $price > $maxPrice) $maxPrice = $price;
            }

            $event->update([
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ]);

            // Create event days
            $currentDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $dayNumber = 1;

            while ($currentDate->lte($endDate)) {
                EventDay::create([
                    'event_id' => $event->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'name' => 'Hari ' . $dayNumber,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                ]);
                $currentDate->addDay();
                $dayNumber++;
            }

            DB::commit();

            return redirect()->route('organizer.events.show', $event)
                ->with('success', 'Event berhasil dibuat. Anda dapat mempublikasikan event sekarang.');

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
            'paid_orders' => $event->orders()->whereIn('status', ['paid', 'completed'])->count(),
            'total_revenue' => $event->orders()->whereIn('status', ['paid', 'completed'])->sum('subtotal'),
            'tickets_sold' => $event->orders()
                ->whereIn('status', ['paid', 'completed'])
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->sum('order_items.quantity'),
        ];

        return view('organizer.events.show', compact('event', 'stats'));
    }

    public function edit(Event $event): View
    {
        $this->authorizeEvent($event);

        if (!$event->status->canEdit()) {
            abort(403, 'Event yang sudah dipublikasi tidak dapat diedit');
        }

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $event->load(['tickets.variants']);

        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        if (!$event->status->canEdit()) {
            return back()->with('error', 'Event yang sudah dipublikasi tidak dapat diedit');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_online' => 'boolean',
            'online_url' => 'nullable|url|required_if:is_online,true',
        ]);

        try {
            // Upload poster baru jika ada
            if ($request->hasFile('poster')) {
                if ($event->poster) {
                    \Storage::disk('public')->delete($event->poster);
                }
                $validated['poster'] = $request->file('poster')->store('events/posters', 'public');
            }

            $event->update($validated);

            return redirect()->route('organizer.events.show', $event)
                ->with('success', 'Event berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui event: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        if (!$event->status->canDelete()) {
            return back()->with('error', 'Event yang sudah dipublikasi tidak dapat dihapus');
        }

        if ($event->orders()->exists()) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah ada pesanan');
        }

        try {
            if ($event->poster) {
                \Storage::disk('public')->delete($event->poster);
            }
            $event->delete();

            return redirect()->route('organizer.events.index')
                ->with('success', 'Event berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus event');
        }
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        if (!$event->status->canPublish()) {
            return back()->with('error', 'Event tidak dapat dipublikasikan');
        }

        // Validasi kelengkapan
        $errors = [];

        if (!$event->poster) {
            $errors[] = 'Poster event wajib diupload';
        }

        if (strlen($event->description) < 100) {
            $errors[] = 'Deskripsi minimal 100 karakter';
        }

        if ($event->tickets()->count() === 0) {
            $errors[] = 'Minimal 1 jenis tiket harus dibuat';
        }

        if ($event->start_date->isPast()) {
            $errors[] = 'Tanggal event sudah lewat';
        }

        // Check email verification
        if (!auth()->user()->hasVerifiedEmail()) {
            $errors[] = 'Email Anda belum diverifikasi. Silakan cek inbox email Anda.';
        }

        if (!empty($errors)) {
            return back()->with('error', implode(', ', $errors));
        }

        $event->update([
            'status' => EventStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Event berhasil dipublikasikan dan sekarang dapat dilihat oleh publik');
    }

    public function unpublish(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        if (!$event->status->canUnpublish()) {
            return back()->with('error', 'Event tidak dapat di-unpublish');
        }

        // Check if ada orders yang paid
        $paidOrders = $event->orders()->whereIn('status', ['paid', 'completed'])->count();
        if ($paidOrders > 0) {
            return back()->with('error', "Event tidak dapat di-unpublish karena sudah ada {$paidOrders} tiket terjual");
        }

        $event->update([
            'status' => EventStatus::DRAFT,
            'published_at' => null,
        ]);

        // Release reserved tickets
        foreach ($event->orders()->where('status', 'pending')->get() as $order) {
            foreach ($order->items as $item) {
                $item->ticketVariant?->decrement('reserved_count', $item->quantity);
            }
            $order->update(['status' => 'expired']);
        }

        return back()->with('success', 'Event berhasil di-unpublish dan tidak lagi terlihat oleh publik');
    }

    protected function authorizeEvent(Event $event): void
    {
        $organizer = auth()->user()->organizer;

        if (!$organizer || $event->organizer_id !== $organizer->id) {
            abort(403);
        }
    }
}
