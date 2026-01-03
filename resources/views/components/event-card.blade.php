@props(['event'])

<article class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition group">
    <a href="{{ route('events.show', $event) }}" class="block">
        <!-- Image -->
        <div class="aspect-video bg-gray-200 overflow-hidden">
            <img src="{{ $event->poster_url }}"
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                 onerror="this.src='https://placehold.co/400x300?text=No+Image'">
        </div>

        <!-- Content -->
        <div class="p-4">
            <!-- Category Badge -->
            <div class="mb-2">
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                    {{ $event->category->name ?? 'Event' }}
                </span>
            </div>

            <!-- Title -->
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition">
                {{ $event->title }}
            </h3>

            <!-- Date & Location -->
            <div class="space-y-1 text-sm text-gray-500 mb-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $event->formatted_date }}
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    {{ $event->formatted_location }}
                </div>
            </div>

            <!-- Price -->
            <div class="pt-3 border-t border-gray-100">
                <span class="text-lg font-bold text-blue-600">
                    {{ $event->formatted_price }}
                </span>
            </div>
        </div>
    </a>
</article>
