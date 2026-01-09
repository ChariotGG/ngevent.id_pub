<x-organizer-layout>
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-[#FF8FC7] mb-8">Organizer Dashboard</h1>

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-[#FF8FC7] to-pink-400 rounded-xl p-6 mb-8">
            <h2 class="text-lg font-semibold text-black">Selamat datang, {{ auth()->user()->organizer->name ?? auth()->user()->name }}!</h2>
            <p class="mt-2 text-black/80">Kelola event dan tiket kamu dari sini.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Events -->
            <div class="bg-gray-950 border border-gray-900 rounded-xl shadow-lg p-6 hover:border-[#FF8FC7] transition">
                <h3 class="text-sm font-medium text-gray-400">Total Events</h3>
                <p class="text-3xl font-bold text-[#FF8FC7] mt-2">
                    {{ auth()->user()->organizer?->events()->count() ?? 0 }}
                </p>
            </div>

            <!-- Published Events -->
            <div class="bg-gray-950 border border-gray-900 rounded-xl shadow-lg p-6 hover:border-green-500 transition">
                <h3 class="text-sm font-medium text-gray-400">Published</h3>
                <p class="text-3xl font-bold text-green-400 mt-2">
                    {{ auth()->user()->organizer?->events()->where('status', 'published')->count() ?? 0 }}
                </p>
            </div>

            <!-- Tickets Sold -->
            <div class="bg-gray-950 border border-gray-900 rounded-xl shadow-lg p-6 hover:border-blue-500 transition">
                <h3 class="text-sm font-medium text-gray-400">Tiket Terjual</h3>
                <p class="text-3xl font-bold text-blue-400 mt-2">0</p>
            </div>

            <!-- Revenue -->
            <div class="bg-gray-950 border border-gray-900 rounded-xl shadow-lg p-6 hover:border-purple-500 transition">
                <h3 class="text-sm font-medium text-gray-400">Pendapatan</h3>
                <p class="text-3xl font-bold text-purple-400 mt-2">Rp 0</p>
            </div>
        </div>

        <!-- Events Section -->
        <div class="bg-gray-950 border border-gray-900 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-[#FF8FC7]">Events Saya</h2>
                <a href="{{ route('organizer.events.create') }}" class="bg-[#FF8FC7] text-black px-4 py-2 rounded-lg font-medium hover:bg-pink-400 transition">
                    + Buat Event Baru
                </a>
            </div>

            <!-- Empty State -->
            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-4 text-gray-400">Belum ada event. Buat event pertamamu sekarang!</p>
            </div>
        </div>
    </div>
</x-organizer-layout>