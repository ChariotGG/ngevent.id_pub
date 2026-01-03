<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Organizer Dashboard</h1>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-blue-800">Selamat datang, {{ auth()->user()->organizer->name ?? auth()->user()->name }}!</h2>
            <p class="mt-2 text-blue-700">Kelola event dan tiket kamu dari sini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Events</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                    {{ auth()->user()->organizer?->events()->count() ?? 0 }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Published</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ auth()->user()->organizer?->events()->where('status', 'published')->count() ?? 0 }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Tiket Terjual</h3>
                <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Pendapatan</h3>
                <p class="text-3xl font-bold text-purple-600 mt-2">Rp 0</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Events Saya</h2>
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                    + Buat Event Baru
                </a>
            </div>

            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-4">Belum ada event. Buat event pertamamu sekarang!</p>
            </div>
        </div>
    </div>
</x-app-layout>
