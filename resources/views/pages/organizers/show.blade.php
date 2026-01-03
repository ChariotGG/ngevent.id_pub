<x-app-layout>
    <div class="bg-white">
        <!-- Header Banner -->
        <div class="relative h-48 md:h-64 bg-gray-900">
            @if($organizer->banner_url)
                <img src="{{ $organizer->banner_url }}" alt="{{ $organizer->name }}" class="w-full h-full object-cover opacity-70">
            @else
                <div class="w-full h-full bg-gradient-to-r from-blue-600 to-blue-800"></div>
            @endif
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Card -->
            <div class="relative -mt-16 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 md:flex md:items-center md:gap-6">
                    <img src="{{ $organizer->logo_url }}" alt="{{ $organizer->name }}"
                         class="w-24 h-24 md:w-32 md:h-32 rounded-xl object-cover border-4 border-white shadow-lg mx-auto md:mx-0">
                    <div class="text-center md:text-left mt-4 md:mt-0 flex-1">
                        <div class="flex items-center justify-center md:justify-start gap-2">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $organizer->name }}</h1>
                            @if($organizer->is_verified)
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        @if($organizer->city)
                            <p class="text-gray-600 mt-1">{{ $organizer->city }}, {{ $organizer->province }}</p>
                        @endif
                        @if($organizer->bio)
                            <p class="text-gray-600 mt-2">{{ $organizer->bio }}</p>
                        @endif
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-4 justify-center md:justify-end">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $organizer->published_events_count }}</p>
                            <p class="text-sm text-gray-500">Events</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($organizer->total_tickets_sold) }}</p>
                            <p class="text-sm text-gray-500">Tiket Terjual</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            @if($organizer->socialLinks->count() > 0)
            <div class="mb-8 flex flex-wrap gap-3 justify-center md:justify-start">
                @foreach($organizer->socialLinks as $link)
                    <a href="{{ $link->url }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200 transition">
                        {{ ucfirst($link->platform) }}
                    </a>
                @endforeach
            </div>
            @endif

            <!-- Events -->
            <div class="py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Events by {{ $organizer->name }}</h2>

                @if($events->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($events as $event)
                            <x-event-card :event="$event" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <p class="text-gray-500">Belum ada event dari organizer ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
