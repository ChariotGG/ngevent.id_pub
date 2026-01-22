<x-app-layout>
    {{-- 
       PENTING: 
       Pastikan file gambar background ada di: public/image/hero-bg.png
    --}}
    
    <section class="relative overflow-hidden min-h-[600px] flex items-center bg-gray-900">
        
        {{-- 1. BACKGROUND IMAGE --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat scale-105 blur-[2px]"
             style="background-image: url('{{ asset('image/hero-bg.png') }}');">
        </div>

        {{-- 2. DARK OVERLAY (Agar teks terbaca jelas) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-purple-900/20"></div>

        {{-- 3. CONTENT HERO --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 text-center">

            {{-- Headline --}}
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 text-white leading-tight drop-shadow-2xl">
                Temukan Event <br class="hidden md:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF8FC7] to-pink-500">
                    Seru di Indonesia
                </span>
            </h1>

            <p class="text-lg md:text-2xl text-gray-200 mb-10 max-w-3xl mx-auto font-light leading-relaxed">
                Cosplay, Music, Sports – Semua pengalaman tak terlupakan dimulai di sini.
            </p>

            {{-- Search Bar Modern --}}
            <div class="max-w-3xl mx-auto">
                <form action="{{ route('events.index') }}" method="GET">
                    <div class="relative group p-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex flex-col sm:flex-row gap-2 transition-all duration-300 hover:bg-white/15 focus-within:border-[#FF8FC7] focus-within:ring-2 focus-within:ring-[#FF8FC7]/30">
                        
                        <input type="text" name="search" placeholder="Cari konser, artis, atau lokasi..."
                               class="flex-1 bg-transparent border-none focus:ring-0 text-white placeholder-gray-300 text-lg px-4 py-3 rounded-xl outline-none w-full">

                        <button type="submit" class="sm:w-auto w-full bg-[#FF8FC7] text-black font-bold text-lg px-8 py-3 rounded-xl hover:bg-pink-400 transition-all shadow-[0_0_15px_rgba(255,143,199,0.4)] hover:shadow-[0_0_25px_rgba(255,143,199,0.6)]">
                            Cari Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-16 bg-black border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white mb-2">Kategori Event</h2>
                <p class="text-gray-400">Pilih kategori sesuai minatmu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('events.category', $category) }}"
                       class="group relative overflow-hidden p-6 bg-gray-900 border border-gray-800 rounded-2xl hover:border-[#FF8FC7]/50 transition-all duration-300 hover:-translate-y-1">
                        
                        {{-- Hover Glow Effect --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FF8FC7]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10">
                            <h3 class="text-xl font-bold text-white group-hover:text-[#FF8FC7] transition-colors mb-2">
                                {{ $category->name }}
                            </h3>
                            <div class="flex items-center text-sm text-gray-400 group-hover:text-gray-300">
                                <span class="w-2 h-2 bg-[#FF8FC7] rounded-full mr-2"></span>
                                {{ $category->events_count }} events aktif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($featuredEvents->count() > 0)
    <section class="py-16 bg-gradient-to-b from-gray-900 to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">Event Unggulan</h2>
                    <p class="text-gray-400">Pilihan editor yang wajib kamu ikuti</p>
                </div>
                <a href="{{ route('events.index') }}" class="hidden md:flex items-center text-[#FF8FC7] hover:text-pink-300 font-medium transition group">
                    Lihat Semua
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
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

    <section class="py-16 bg-black border-t border-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">Event Mendatang</h2>
                    <p class="text-gray-400">Jangan sampai ketinggalan momen seru</p>
                </div>
                <a href="{{ route('events.index') }}" class="hidden md:flex items-center text-[#FF8FC7] hover:text-pink-300 font-medium transition group">
                    Lihat Semua
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($upcomingEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>

            @if($upcomingEvents->count() === 0)
                <div class="text-center py-16 bg-gray-900/50 rounded-2xl border border-gray-800 border-dashed">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 mb-4">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-white mb-2">Belum Ada Event</h3>
                    <p class="text-gray-500">Jadilah yang pertama membuat event!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Organizer Event -->
    <section class="relative py-20 bg-gray-900 border-t border-gray-800 overflow-hidden">        
        {{-- Background Glow Effect --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-[#FF8FC7]/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-purple-900/20 rounded-full blur-[100px]"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6 text-white">
                Punya Event?
            </h2>
            <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">
                Daftarkan event kamu di ngevent.id. Kelola tiket lebih mudah, jangkau audiens lebih luas.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                <a href="{{ route('register.organizer') }}" class="px-8 py-4 bg-[#FF8FC7] text-black font-bold rounded-xl hover:bg-pink-400 transition shadow-[0_0_20px_rgba(255,143,199,0.3)]">
                    Daftar Sebagai Organizer
                </a>
            </div>
        </div>
    </section>

</x-app-layout>