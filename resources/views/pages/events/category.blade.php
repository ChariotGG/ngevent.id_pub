<x-app-layout>
    <div class="bg-white">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="text-sm mb-4">
                    <a href="{{ route('home') }}" class="text-blue-200 hover:text-white">Home</a>
                    <span class="mx-2 text-blue-300">/</span>
                    <a href="{{ route('events.index') }}" class="text-blue-200 hover:text-white">Events</a>
                    <span class="mx-2 text-blue-300">/</span>
                    <span>{{ $category->name }}</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-bold">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="mt-2 text-blue-100">{{ $category->description }}</p>
                @endif
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Subcategories -->
            @if($category->subcategories->count() > 0)
            <div class="mb-8">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('events.category', $category) }}"
                       class="px-4 py-2 rounded-full text-sm font-medium bg-blue-600 text-white">
                        Semua
                    </a>
                    @foreach($category->subcategories as $subcategory)
                        <a href="{{ route('events.category', $category) }}?subcategory={{ $subcategory->id }}"
                           class="px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            {{ $subcategory->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Results Info -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    Menampilkan <span class="font-semibold">{{ $events->total() }}</span> event
                </p>
            </div>

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($events as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada event di kategori ini</h3>
                    <p class="mt-2 text-gray-500">Cek kembali nanti atau lihat event di kategori lain</p>
                    <a href="{{ route('events.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Semua Event →
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
