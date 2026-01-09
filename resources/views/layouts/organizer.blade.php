<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Organizer Dashboard' }} - ngevent.id</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-950 border-r border-gray-900 transform transition-transform duration-300 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-900">
                <a href="{{ route('organizer.dashboard') }}" class="flex items-center">
                    <img
                        src="{{ asset('image/ngevent.id_logo.png') }}"
                        alt="ngevent.id"
                        class="h-7 object-contain"
                    >
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <a href="{{ route('organizer.dashboard') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition {{ request()->routeIs('organizer.dashboard') ? 'bg-[#FF8FC7] text-black' : 'text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('organizer.events.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition {{ request()->routeIs('organizer.events.*') ? 'bg-[#FF8FC7] text-black' : 'text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Event Saya
                    </a>

                    <a href="{{ route('organizer.orders.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition {{ request()->routeIs('organizer.orders.*') ? 'bg-[#FF8FC7] text-black' : 'text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Pesanan
                    </a>

                    <a href="{{ route('organizer.attendees.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition {{ request()->routeIs('organizer.attendees.*') ? 'bg-[#FF8FC7] text-black' : 'text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Peserta
                    </a>
                </div>

                <div class="mt-8">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaturan</p>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('organizer.settings.index') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition {{ request()->routeIs('organizer.settings.*') ? 'bg-[#FF8FC7] text-black' : 'text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Pengaturan
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Back to Site -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-900">
                <a href="/" class="flex items-center px-4 py-3 text-sm font-medium text-gray-400 hover:text-[#FF8FC7] transition rounded-lg hover:bg-gray-900">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Kembali ke Website
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Header -->
            <header class="bg-gray-950 border-b border-gray-900 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex-1 lg:flex-none"></div>

                <!-- User Menu -->
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-400">{{ auth()->user()->organizer->name ?? auth()->user()->name }}</span>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="w-10 h-10 bg-[#FF8FC7] rounded-full flex items-center justify-center text-black font-semibold hover:bg-pink-400 transition">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </button>
                        <div x-show="open" @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-gray-950 border border-gray-800 rounded-lg shadow-lg py-2 z-50">
                            <a href="{{ route('organizer.settings.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#FF8FC7]/10 hover:text-[#FF8FC7]">Pengaturan</a>
                            <hr class="my-2 border-gray-800">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-6 mt-4 bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-4 bg-red-500/10 border border-red-500 text-red-400 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:leave="transition-opacity ease-linear duration-300"></div>
</body>
</html>