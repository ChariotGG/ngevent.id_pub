<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ngevent.id - Platform Ticketing Event Indonesia' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased bg-black">
<div class="min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav
        x-data="{ scrolled: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 10)"
        :class="scrolled
            ? 'bg-gray-950/80 backdrop-blur border-gray-900'
            : 'bg-gray-950 border-gray-900'"
        class="border-b sticky top-0 z-50 transition-all duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img
                            src="{{ asset('image/ngevent.id_logo.png') }}"
                            alt="ngevent.id"
                            class="h-7 md:h-8 object-contain"
                        >
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex md:items-center md:space-x-6">
                    <a href="{{ route('events.index') }}"
                       class="text-gray-300 hover:text-[#FF8FC7] transition">
                        Events
                    </a>

                    <a href="{{ route('tickets.lookup') }}"
                       class="text-gray-300 hover:text-[#FF8FC7] transition">
                        Cek Tiket
                    </a>
                </div>

                <!-- Right Side -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    @auth
                        {{-- Menu untuk Organizer --}}
                        @if(auth()->user()->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}"
                               class="text-gray-300 hover:text-[#FF8FC7] transition">
                                Dashboard
                            </a>
                        @endif

                        {{-- Menu untuk Admin --}}
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="text-gray-300 hover:text-[#FF8FC7] transition">
                                Admin Panel
                            </a>
                        @endif

                        {{-- Dropdown User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 text-gray-300 hover:text-[#FF8FC7] transition">
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-gray-950 border border-gray-800 rounded-lg shadow-lg py-1 z-50">

                                @if(auth()->user()->isOrganizer())
                                    <a href="{{ route('organizer.events.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#FF8FC7]/10 hover:text-[#FF8FC7]">
                                        Event Saya
                                    </a>
                                    <a href="{{ route('organizer.orders.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#FF8FC7]/10 hover:text-[#FF8FC7]">
                                        Pesanan
                                    </a>
                                    <a href="{{ route('organizer.settings.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#FF8FC7]/10 hover:text-[#FF8FC7]">
                                        Pengaturan
                                    </a>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.events.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#FF8FC7]/10 hover:text-[#FF8FC7]">
                                        Kelola Events
                                    </a>
                                @endif

                                <hr class="my-1 border-gray-800">

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-gray-300 hover:text-[#FF8FC7] transition">
                            Masuk
                        </a>
                        <a href="{{ route('register.organizer') }}"
                           class="bg-[#FF8FC7] text-black px-4 py-2 rounded-lg hover:bg-pink-400 transition font-semibold">
                            Daftar Organizer
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu -->
                <div class="md:hidden flex items-center">
                    <div x-data="{ mobileOpen: false }">
                        <button @click="mobileOpen = !mobileOpen" class="text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <div x-show="mobileOpen" @click.away="mobileOpen = false"
                             class="absolute top-16 left-0 right-0 bg-gray-950 border-t border-gray-800 shadow-lg p-4 z-50">

                            <a href="{{ route('events.index') }}"
                               class="block py-2 text-gray-300 hover:text-[#FF8FC7]">
                                Events
                            </a>

                            <a href="{{ route('tickets.lookup') }}"
                               class="block py-2 text-gray-300 hover:text-[#FF8FC7]">
                                Cek Tiket
                            </a>

                            @auth
                                @if(auth()->user()->isOrganizer())
                                    <a href="{{ route('organizer.dashboard') }}"
                                       class="block py-2 text-gray-300 hover:text-[#FF8FC7]">
                                        Dashboard
                                    </a>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="block py-2 text-gray-300 hover:text-[#FF8FC7]">
                                        Admin Panel
                                    </a>
                                @endif

                                <hr class="my-2 border-gray-800">

                                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-red-400 hover:text-red-300">Keluar</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}"
                                   class="block py-2 text-gray-300 hover:text-[#FF8FC7]">
                                    Masuk
                                </a>
                                <a href="{{ route('register.organizer') }}"
                                   class="block py-2 text-[#FF8FC7] font-semibold">
                                    Daftar Organizer
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 mx-4 mt-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500 text-red-400 px-4 py-3 mx-4 mt-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Page Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-900 text-gray-400 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">

            <!-- Logo -->
            <div class="flex justify-center mb-3">
                <img
                    src="{{ asset('image/ngevent.id_logo.png') }}"
                    alt="ngevent.id"
                    class="h-9 md:h-10 object-contain"
                >
            </div>

            <p class="text-sm text-gray-500">
                Platform ticketing event terpercaya di Indonesia
            </p>

            <div class="border-t border-gray-900 mt-6 pt-4">
                <p class="text-xs text-gray-600">
                    &copy; {{ date('Y') }} ngevent.id. All rights reserved.
                </p>
            </div>

        </div>
    </footer>

</div>

@stack('scripts')
</body>
</html>
