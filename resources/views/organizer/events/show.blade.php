<x-organizer-layout>
    <div class="max-w-5xl">
        <nav class="text-sm mb-6">
            <a href="{{ route('organizer.events.index') }}" class="text-blue-600 hover:text-blue-800">← Kembali ke Event Saya</a>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-gray-600 mt-1">{{ $event->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($event->status === 'draft')
                            <form action="{{ route('organizer.events.publish', $event) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Publish
                                </button>
                            </form>
                        @elseif($event->status === 'published')
                            <form action="{{ route('organizer.events.unpublish', $event) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                    Unpublish
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('organizer.events.edit', $event) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Edit
                        </a>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-4">
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'published' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$event->status] ?? 'bg-gray-100' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                    @if($event->status === 'published')
                        <a href="{{ route('events.show', $event) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                            Lihat Halaman Publik →
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Pesanan Lunas</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['paid_orders'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Tiket Terjual</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['tickets_sold'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Pendapatan</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Event Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-gray-900">Detail Event</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Tanggal</p>
                                <p class="font-medium">{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Waktu</p>
                                <p class="font-medium">{{ $event->start_time }} - {{ $event->end_time }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lokasi</p>
                            <p class="font-medium">{{ $event->venue_name }}</p>
                            <p class="text-gray-600">{{ $event->venue_address }}</p>
                            <p class="text-gray-600">{{ $event->city }}, {{ $event->province }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Deskripsi</p>
                            <p class="text-gray-600 whitespace-pre-line">{{ $event->description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tickets -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-gray-900">Tiket</h2>
                    </div>
                    <div class="divide-y">
                        @foreach($event->tickets as $ticket)
                            @foreach($ticket->variants as $variant)
                                <div class="p-4 flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $ticket->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $ticket->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">
                                            @if($variant->price > 0)
                                                Rp {{ number_format($variant->price, 0, ',', '.') }}
                                            @else
                                                GRATIS
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $variant->sold_count }}/{{ $variant->stock }} terjual
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('organizer.attendees.event', $event) }}"
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-gray-700">Lihat Peserta</span>
                        </a>
                        <a href="{{ route('organizer.orders.index', ['event_id' => $event->id]) }}"
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="text-gray-700">Lihat Pesanan</span>
                        </a>
                        <form action="{{ route('organizer.events.duplicate', $event) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-left">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-700">Duplikasi Event</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Danger Zone -->
                @if($event->orders_count === 0)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-red-200">
                        <div class="p-6 border-b border-red-200 bg-red-50">
                            <h2 class="text-lg font-semibold text-red-800">Zona Berbahaya</h2>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('organizer.events.destroy', $event) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-50 transition">
                                    Hapus Event
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-organizer-layout>
