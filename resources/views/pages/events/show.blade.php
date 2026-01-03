<x-app-layout>
    <div class="bg-white">
        <!-- Hero Banner -->
        <div class="relative h-64 md:h-96 bg-gray-900">
            @if($event->banner_url)
                <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover opacity-50">
            @else
                <div class="w-full h-full bg-gradient-to-r from-blue-600 to-blue-800"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 -mt-32 relative z-10 pb-16">
                <!-- Left Column -->
                <div class="flex-1">
                    <!-- Event Poster & Title Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="md:flex">
                            <!-- Poster -->
                            <div class="md:w-1/3">
                                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}"
                                     class="w-full h-64 md:h-full object-cover"
                                     onerror="this.src='https://placehold.co/400x600?text=No+Image'">
                            </div>
                            <!-- Info -->
                            <div class="md:w-2/3 p-6">
                                <!-- Category Badge -->
                                <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 mb-3">
                                    {{ $event->category->name }}
                                </span>

                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>

                                <!-- Date & Time -->
                                <div class="flex items-center text-gray-600 mb-3">
                                    <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium">{{ $event->formatted_date }}</p>
                                        @if($event->formatted_time)
                                            <p class="text-sm">{{ $event->formatted_time }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-start text-gray-600 mb-3">
                                    <svg class="w-5 h-5 mr-3 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        @if($event->is_online)
                                            <p class="font-medium">Online Event</p>
                                        @else
                                            <p class="font-medium">{{ $event->venue_name }}</p>
                                            <p class="text-sm">{{ $event->venue_address }}</p>
                                            <p class="text-sm">{{ $event->city }}, {{ $event->province }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Organizer -->
                                <div class="flex items-center text-gray-600 pt-4 border-t">
                                    <img src="{{ $event->organizer->logo_url }}" alt="{{ $event->organizer->name }}"
                                         class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Diselenggarakan oleh</p>
                                        <a href="{{ route('organizer.show', $event->organizer) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                            {{ $event->organizer->name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tentang Event</h2>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- Event Days (if multi-day) -->
                    @if($event->days->count() > 1)
                    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Jadwal Event</h2>
                        <div class="space-y-3">
                            @foreach($event->days as $day)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <div class="w-16 h-16 bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center mr-4">
                                        <span class="text-2xl font-bold">{{ $day->date->format('d') }}</span>
                                        <span class="text-xs">{{ $day->date->format('M') }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $day->name ?? 'Day ' . $loop->iteration }}</p>
                                        <p class="text-sm text-gray-600">{{ $day->date->format('l, d F Y') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column - Ticket Card -->
                <div class="lg:w-96">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Tiket</h2>
                        <p class="text-3xl font-bold text-blue-600 mb-6">{{ $event->formatted_price }}</p>

                        @if($event->tickets->count() > 0)
                            <div class="space-y-4 mb-6">
                                @foreach($event->tickets as $ticket)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-semibold text-gray-900">{{ $ticket->name }}</h3>
                                            <span class="text-blue-600 font-bold">
                                                @if($ticket->variants->min('price') == 0)
                                                    Gratis
                                                @else
                                                    Rp {{ number_format($ticket->variants->min('price'), 0, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>
                                        @if($ticket->description)
                                            <p class="text-sm text-gray-600 mb-2">{{ $ticket->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">
                                            Tersedia: {{ $ticket->variants->sum(fn($v) => $v->stock - $v->sold_count - $v->reserved_count) }} tiket
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            @auth
                                <a href="{{ route('checkout.index', $event) }}"
                                   class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Beli Tiket
                                </a>
                            @else
                                <a href="{{ route('login') }}?redirect={{ urlencode(route('checkout.index', $event)) }}"
                                   class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Login untuk Beli Tiket
                                </a>
                            @endauth
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>Tiket belum tersedia</p>
                            </div>
                        @endif

                        <!-- Share -->
                        <div class="mt-6 pt-6 border-t">
                            <p class="text-sm text-gray-600 mb-3">Bagikan event ini:</p>
                            <div class="flex gap-2">
                                <a href="https://wa.me/?text={{ urlencode($event->title . ' - ' . url()->current()) }}"
                                   target="_blank"
                                   class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-center text-sm hover:bg-gray-50 transition">
                                    WhatsApp
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($event->title) }}&url={{ urlencode(url()->current()) }}"
                                   target="_blank"
                                   class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-center text-sm hover:bg-gray-50 transition">
                                    Twitter
                                </a>
                                <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); alert('Link copied!')"
                                        class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-center text-sm hover:bg-gray-50 transition">
                                    Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Events -->
            @if($relatedEvents->count() > 0)
            <div class="py-16 border-t">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Event Serupa</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedEvents as $relatedEvent)
                        <x-event-card :event="$relatedEvent" />
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
