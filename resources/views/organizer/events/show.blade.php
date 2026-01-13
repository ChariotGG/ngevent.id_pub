<x-organizer-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        @if($event->status->value === 'draft') bg-gray-100 text-gray-700
                        @elseif($event->status->value === 'published') bg-green-100 text-green-700
                        @else bg-blue-100 text-blue-700
                        @endif">
                        {{ $event->status->label() }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $event->category->name }} • {{ $event->city }}</p>
            </div>

            <div class="flex items-center gap-2">
                @if($event->status->canEdit())
                    <a href="{{ route('organizer.events.edit', $event) }}"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        Edit
                    </a>
                @endif

                @if($event->status->canPublish())
                    <form method="POST" action="{{ route('organizer.events.publish', $event) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700">
                            Publikasikan Event
                        </button>
                    </form>
                @endif

                @if($event->status->canUnpublish())
                    <form method="POST" action="{{ route('organizer.events.unpublish', $event) }}" class="inline"
                        onsubmit="return confirm('Yakin ingin unpublish event ini?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                            Unpublish
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Pesanan Dibayar</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['paid_orders'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Tiket Terjual</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['tickets_sold'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Event Info --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Event</h2>

                    @if($event->poster)
                        <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}"
                            class="w-full h-64 object-cover rounded-lg mb-4">
                    @endif

                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</p>
                                <p class="text-gray-600">{{ $event->start_time }} - {{ $event->end_time }} WIB</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">{{ $event->venue_name }}</p>
                                <p class="text-gray-600">{{ $event->venue_address }}, {{ $event->city }}, {{ $event->province }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="font-medium text-gray-900 mb-2">Deskripsi</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                    </div>
                </div>

                {{-- Tickets --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tiket</h2>

                    <div class="space-y-3">
                        @foreach($event->tickets as $ticket)
                            @foreach($ticket->variants as $variant)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $ticket->name }}</p>
                                        <p class="text-sm text-gray-600">
                                            Stok: {{ $variant->stock }} • Terjual: {{ $variant->sold_count }} • Tersisa: {{ $variant->available_stock }}
                                        </p>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">
                                        @if($variant->price == 0)
                                            Gratis
                                        @else
                                            Rp {{ number_format($variant->price, 0, ',', '.') }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                @if($event->status->value === 'draft')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="font-medium text-yellow-900 mb-2">Event belum dipublikasi</h3>
                        <p class="text-sm text-yellow-800 mb-3">Pastikan semua informasi sudah benar sebelum publikasi.</p>

                        <ul class="text-xs text-yellow-700 space-y-1 mb-4">
                            <li class="flex items-center gap-2">
                                @if($event->poster)
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Poster event
                            </li>
                            <li class="flex items-center gap-2">
                                @if(strlen($event->description) >= 100)
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Deskripsi lengkap (min. 100 karakter)
                            </li>
                            <li class="flex items-center gap-2">
                                @if($event->tickets->count() > 0)
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Minimal 1 tiket
                            </li>
                            <li class="flex items-center gap-2">
                                @if(auth()->user()->hasVerifiedEmail())
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Email terverifikasi
                            </li>
                        </ul>

                        @if($event->status->canPublish())
                            <form method="POST" action="{{ route('organizer.events.publish', $event) }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700">
                                    Publikasikan Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if(!auth()->user()->hasVerifiedEmail())
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-medium text-blue-900 mb-2">Verifikasi Email</h3>
                        <p class="text-sm text-blue-800 mb-3">Email Anda belum diverifikasi. Cek inbox email Anda.</p>
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 border border-blue-300 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100">
                                Kirim Ulang Email Verifikasi
                            </button>
                        </form>
                    </div>
                @endif

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-3">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('organizer.orders.index', ['event_id' => $event->id]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg border border-gray-200">
                            Lihat Pesanan
                        </a>
                        @if($event->status->value === 'published')
                            <a href="{{ route('events.show', $event->slug) }}" target="_blank"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg border border-gray-200">
                                Lihat di Halaman Publik
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-organizer-layout>
