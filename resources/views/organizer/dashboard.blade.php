<x-app-layout>
    <!-- Hero Section -->
<section class="bg-gradient-to-br from-black via-gray-900 to-black text-[#FF8FC7]">
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
                           class="flex-1 px-6 py-4 rounded-lg bg-gray-900 text-[#FF8FC7] border border-gray-800
                                  placeholder-gray-500 focus:ring-2 focus:ring-[#FF8FC7] focus:outline-none">

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
    <section class="py-12 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#FF8FC7] mb-8 text-center">Kategori Event</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('events.category', $category) }}"
                       class="group p-6 bg-gray-900 border border-gray-800 rounded-xl hover:bg-gray-800 hover:border-[#FF8FC7] transition">
                        <h3 class="text-xl font-bold text-[#FF8FC7] mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-400">{{ $category->events_count }} events</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Events Section -->
    @if($featuredEvents->count() > 0)
    <section class="py-16 bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-[#FF8FC7]">Event Unggulan</h2>
                <a href="{{ route('events.index') }}" class="text-[#FF8FC7] hover:text-pink-400 font-medium">
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
    <section class="py-16 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-[#FF8FC7]">Event Mendatang</h2>
                <a href="{{ route('events.index') }}" class="text-[#FF8FC7] hover:text-pink-400 font-medium">
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
<section class="py-16 bg-gradient-to-br from-[#FF8FC7] to-pink-400">
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