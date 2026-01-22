<x-app-layout>
    <!-- Hero Section -->
    <section class="relative bg-background overflow-hidden">
        <!-- Gradient Background Effect -->
        <div class="absolute inset-0 bg-gradient-radial-pink"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="text-center">
                <div class="inline-block mb-4 px-4 py-2 bg-primary/10 border border-primary/20 rounded-full">
                    <span class="text-primary text-sm font-medium">🎉 Platform Ticketing Event Terpercaya</span>
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                    <span class="text-text-primary">Temukan Event</span>
                    <span class="block text-gradient">
                        Seru di Indonesia
                    </span>
                </h1>

                <p class="text-xl md:text-2xl text-text-secondary mb-10 max-w-3xl mx-auto">
                    Cosplay, Music, Sports – Semua event favoritmu ada di sini
                </p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <form action="{{ route('events.index') }}" method="GET"
                          class="flex flex-col sm:flex-row gap-3 backdrop-blur-sm">

                        <input type="text" name="search" placeholder="Cari event..."
                               class="form-input flex-1">

                        <button type="submit" class="btn-primary shadow-pink">
                            Cari Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-background border-t border-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="section-title text-text-primary">
                    Kategori Event
                </h2>
                <p class="text-text-secondary">Pilih kategori sesuai minatmu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('events.category', $category) }}"
                       class="feature-card hover-lift group overflow-hidden">
                        <!-- Hover Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="relative">
                            <h3 class="feature-title text-text-primary group-hover:text-primary transition">
                                {{ $category->name }}
                            </h3>
                            <p class="text-text-secondary flex items-center gap-2">
                                <span class="inline-block w-2 h-2 bg-primary rounded-full"></span>
                                {{ $category->events_count }} events
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Events Section -->
    @if($featuredEvents->count() > 0)
    <section class="py-16 bg-gradient-to-b from-background to-background-card">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-text-primary mb-2">Event Unggulan</h2>
                    <p class="text-text-secondary">Event pilihan yang wajib kamu ikuti</p>
                </div>
                <a href="{{ route('events.index') }}"
                   class="hidden md:flex items-center gap-2 link group">
                    <span>Lihat Semua</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
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
    <section class="py-16 bg-background-card border-t border-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-text-primary mb-2">Event Mendatang</h2>
                    <p class="text-text-secondary">Jangan sampai ketinggalan!</p>
                </div>
                <a href="{{ route('events.index') }}"
                   class="hidden md:flex items-center gap-2 link group">
                    <span>Lihat Semua</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($upcomingEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>

            @if($upcomingEvents->count() === 0)
                <div class="empty-state">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="empty-state-title">Belum Ada Event Mendatang</h3>
                    <p class="empty-state-description">Tunggu event seru lainnya. Cek lagi nanti!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 bg-background border-t border-gray-900 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Badge -->
            <div class="inline-block mb-6 px-4 py-2 bg-primary/10 border border-primary/20 rounded-full">
                <span class="text-primary text-sm font-medium">✨ Untuk Event Organizer</span>
            </div>

            <h2 class="text-4xl md:text-5xl font-bold mb-6 text-text-primary">
                Punya Event?
            </h2>

            <p class="text-xl text-text-secondary mb-10 max-w-2xl mx-auto leading-relaxed">
                Daftarkan event kamu di ngevent.id dan jangkau ribuan penggemar di seluruh Indonesia
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register.organizer') }}" class="btn-primary shadow-pink">
                    <span>Daftar Sebagai Organizer</span>
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>

                <a href="{{ route('events.index') }}" class="btn-secondary">
                    <span>Lihat Semua Event</span>
                </a>
            </div>

            <!-- Stats -->
            <div class="mt-16 grid grid-cols-3 gap-8 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-1">1000+</div>
                    <div class="text-sm text-text-tertiary">Events</div>
                </div>
                <div class="text-center border-x border-gray-800">
                    <div class="text-3xl font-bold text-primary mb-1">50K+</div>
                    <div class="text-sm text-text-tertiary">Pengunjung</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-1">100+</div>
                    <div class="text-sm text-text-tertiary">Organizers</div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>
