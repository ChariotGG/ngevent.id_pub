<x-app-layout>
    <!-- Hero Section -->
<section class="bg-gradient-to-b from-gray-950 via-black to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-[#FF8FC7]">
                Temukan Event Seru di Indonesia
            </h1>

            <p class="text-xl md:text-2xl text-[#FF8FC7] mb-8 max-w-3xl mx-auto">
                Cosplay, Music, Sports – Semua event favoritmu ada di sini
            </p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('events.index') }}" method="GET"
                      class="flex flex-col sm:flex-row gap-3">

                    <input type="text" name="search" placeholder="Cari event..."
                           class="flex-1 px-6 py-4 rounded-lg bg-black text-[#FF8FC7] border border-gray-800
                                  placeholder-gray-500 focus:ring-2 focus:ring-[#FF8FC7] focus:border-[#FF8FC7] focus:outline-none transition">

                    <button type="submit"
                            class="bg-[#FF8FC7] text-black font-semibold px-8 py-4 rounded-lg
                                   hover:bg-pink-400 transition">
                        Cari Event
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>


    <!-- Categories Section -->
    <section class="py-12 bg-gradient-to-b from-black via-[#FF8FC7] to-[#FF8FC7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-black mb-8 text-center">Kategori Event</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('events.category', $category) }}"
                       class="group p-6 bg-black/80 backdrop-blur-sm border border-black/50 rounded-xl hover:bg-black hover:border-pink-300 hover:shadow-xl transition-all duration-300">
                        <h3 class="text-xl font-bold text-[#FF8FC7] mb-2 group-hover:text-pink-300 transition">{{ $category->name }}</h3>
                        <p class="text-pink-200">{{ $category->events_count }} events</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Events Section -->
    @if($featuredEvents->count() > 0)
    <section class="py-16 bg-gradient-to-b from-[#FF8FC7] to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-black">Event Unggulan</h2>
                <a href="{{ route('events.index') }}" class="text-black hover:text-gray-800 font-medium transition">
                    Lihat Semua →
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Upcoming Events Section -->
    <section class="py-16 bg-gradient-to-b from-black to-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-[#FF8FC7]">Event Mendatang</h2>
                <a href="{{ route('events.index') }}" class="text-[#FF8FC7] hover:text-pink-400 font-medium transition">
                    Lihat Semua →
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($upcomingEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>

            @if($upcomingEvents->count() === 0)
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Belum ada event mendatang</p>
                </div>
            @endif
        </div>
    </section>


<!-- CTA Section -->
<section class="py-16 bg-gradient-to-b from-gray-950 via-[#FF8FC7] to-[#FF8FC7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-black">
            Punya Event?
        </h2>

        <p class="text-xl mb-8 max-w-2xl mx-auto text-black">
            Daftarkan event kamu di ngevent.id dan jangkau ribuan penggemar di seluruh Indonesia
        </p>

        <a href="{{ route('register.organizer') }}"
           class="inline-block bg-black text-[#FF8FC7] font-semibold px-8 py-4 rounded-lg
                  hover:bg-gray-900 transition shadow-lg">
            Daftar Sebagai Organizer
        </a>

    </div>
</section>



</x-app-layout>