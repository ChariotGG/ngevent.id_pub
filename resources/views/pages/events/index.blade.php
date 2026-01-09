<x-app-layout>
    <div class="bg-black min-h-screen">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#FF8FC7] to-pink-400 text-black py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl md:text-4xl font-bold">Semua Event</h1>
                <p class="mt-2 text-black/80">Temukan event seru yang sesuai dengan minatmu</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <form action="{{ route('events.index') }}" method="GET" class="bg-gray-950 border border-gray-900 rounded-xl p-6 sticky top-24">
                        <h3 class="font-semibold text-[#FF8FC7] mb-4">Filter Event</h3>

                        <!-- Search -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Cari</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Nama event..."
                                   class="w-full px-3 py-2 bg-black text-[#FF8FC7] border border-gray-800 rounded-lg 
                                          placeholder-gray-500 focus:ring-2 focus:ring-[#FF8FC7] focus:border-[#FF8FC7] transition">
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Kategori</label>
                            <select name="category" class="w-full px-3 py-2 bg-black text-[#FF8FC7] border border-gray-800 rounded-lg 
                                                           focus:ring-2 focus:ring-[#FF8FC7] focus:border-[#FF8FC7] transition">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Kota</label>
                            <input type="text" name="city" value="{{ request('city') }}"
                                   placeholder="Nama kota..."
                                   class="w-full px-3 py-2 bg-black text-[#FF8FC7] border border-gray-800 rounded-lg 
                                          placeholder-gray-500 focus:ring-2 focus:ring-[#FF8FC7] focus:border-[#FF8FC7] transition">
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-[#FF8FC7] text-black py-2 px-4 rounded-lg font-medium hover:bg-pink-400 transition">
                                Filter
                            </button>
                            <a href="{{ route('events.index') }}" class="px-4 py-2 border border-gray-800 text-gray-400 rounded-lg hover:bg-gray-900 hover:text-[#FF8FC7] transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </aside>

                <!-- Events Grid -->
                <div class="flex-1">
                    <!-- Results Info -->
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-gray-400">
                            Menampilkan <span class="font-semibold text-[#FF8FC7]">{{ $events->total() }}</span> event
                        </p>
                    </div>

                    @if($events->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($events as $event)
                                <x-event-card :event="$event" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $events->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 bg-gray-950 border border-gray-900 rounded-xl">
                            <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-[#FF8FC7]">Tidak ada event ditemukan</h3>
                            <p class="mt-2 text-gray-500">Coba ubah filter pencarian atau lihat semua event</p>
                            <a href="{{ route('events.index') }}" class="mt-4 inline-block text-[#FF8FC7] hover:text-pink-400 font-medium transition">
                                Lihat Semua Event →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>