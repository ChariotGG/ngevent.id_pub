<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ngevent.id - Platform Ticketing Event Indonesia' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        /* Custom Neon Glow for primary button */
        .btn-neon-pink {
            box-shadow: 0 0 10px rgba(255, 143, 199, 0.3);
        }
        .btn-neon-pink:hover {
            box-shadow: 0 0 15px rgba(255, 143, 199, 0.5);
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-black text-gray-300 h-full">
<div class="min-h-screen flex flex-col">

    <nav
        x-data="{ scrolled: false, mobileOpen: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
        :class="scrolled
            ? 'bg-black/90 backdrop-blur-md border-gray-800 shadow-sm'
            : 'bg-transparent border-transparent'"
        class="fixed top-0 left-0 right-0 z-50 border-b transition-all duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">

                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
                        {{-- Pastikan path image ini benar --}}
                        <img
                            src="{{ asset('image/ngevent.id_logo.png') }}"
                            alt="ngevent.id"
                            class="h-8 md:h-10 w-auto object-contain"
                        >
                    </a>
                </div>

                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="{{ route('events.index') }}"
                       class="text-sm font-medium text-gray-300 hover:text-[#FF8FC7] transition-colors duration-200">
                        Events
                    </a>

                    <a href="{{ route('tickets.lookup') }}"
                       class="text-sm font-medium text-gray-300 hover:text-[#FF8FC7] transition-colors duration-200">
                        Cek Tiket
                    </a>
                </div>

                <div class="hidden md:flex md:items-center md:space-x-6">
                    @auth
                        {{-- Dropdown User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-300 hover:text-[#FF8FC7] transition groupFocus:outline-none">
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            {{-- Dropdown Content --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-56 bg-gray-950 border border-gray-800 rounded-xl shadow-xl py-2 z-50 ring-1 ring-black ring-opacity-5 focus:outline-none">

                                @if(auth()->user()->isOrganizer())
                                    <div class="px-4 py-2 text-xs text-gray-500 font-semibold uppercase tracking-wider">Organizer Area</div>
                                    <a href="{{ route('organizer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]">Dashboard</a>
                                    <a href="{{ route('organizer.events.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]">Event Saya</a>
                                    <div class="border-t border-gray-800 my-1"></div>
                                @endif

                                @if(auth()->user()->isAdmin())
                                     <div class="px-4 py-2 text-xs text-gray-500 font-semibold uppercase tracking-wider">Admin Area</div>
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-[#FF8FC7]">Admin Panel</a>
                                    <div class="border-t border-gray-800 my-1"></div>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-300 hover:text-[#FF8FC7] transition-colors duration-200">
                            Masuk
                        </a>
                        <a href="{{ route('register.organizer') }}"
                           class="btn-neon-pink bg-[#FF8FC7] text-black text-sm px-5 py-2.5 rounded-lg hover:bg-[#ff7abf] transition-all duration-300 font-bold">
                            Daftar Organizer
                        </a>
                    @endauth
                </div>

                <div class="md:hidden flex items-center">
                    <button @click="mobileOpen = !mobileOpen" class="text-gray-300 hover:text-white p-2 -mr-2">
                        <span class="sr-only">Open main menu</span>
                         {{-- Icon Hamburger --}}
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                         {{-- Icon Close (X) --}}
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="mobileOpen = false"
             x-cloak
             class="md:hidden bg-gray-950 border-b border-gray-800 shadow-lg">
            <div class="px-4 pt-2 pb-4 space-y-1 sm:px-3">
                <a href="{{ route('events.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-[#FF8FC7] hover:bg-gray-900">Events</a>
                <a href="{{ route('tickets.lookup') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-[#FF8FC7] hover:bg-gray-900">Cek Tiket</a>
            </div>

            <div class="pt-4 pb-3 border-t border-gray-800">
                @auth
                    <div class="px-4 flex items-center">
                        <div class="text-base font-medium text-white">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        @if(auth()->user()->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-[#FF8FC7] hover:bg-gray-900">Dashboard Organizer</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                             <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-[#FF8FC7] hover:bg-gray-900">Admin Panel</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:text-red-300 hover:bg-red-900/20">Keluar</button>
                        </form>
                    </div>
                @else
                    <div class="mt-3 px-4 space-y-3">
                        <a href="{{ route('login') }}" class="block text-center w-full px-4 py-2 text-sm font-medium text-gray-300 border border-gray-700 rounded-lg hover:border-gray-500 transition">Masuk</a>
                        <a href="{{ route('register.organizer') }}" class="block text-center w-full px-4 py-2 text-sm font-medium text-black bg-[#FF8FC7] rounded-lg hover:bg-[#ff7abf] transition font-bold">Daftar Organizer</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="h-20"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="relative bg-green-500/10 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg mb-4 flex justify-between items-start">
                <div>
                    <p class="font-medium">Berhasil!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-200 ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="relative bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg mb-4 flex justify-between items-start">
                <div>
                     <p class="font-medium">Oops!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
                 <button @click="show = false" class="text-red-400 hover:text-red-200 ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
    </div>

    <main class="flex-1 relative z-0">
        {{ $slot }}
    </main>
    <!-- Footer -->
    <footer class="bg-black border-t border-gray-900 mt-auto z-10 relative pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-8 mb-8">
            <div class="md:col-span-4 flex flex-col items-start">
                <img src="{{ asset('image/ngevent.id_logo.png') }}" alt="ngevent.id" class="h-8 mb-4 object-contain">
                <p class="text-gray-500 text-sm mb-4 leading-relaxed">
                    Platform ticketing event paling seru dan terpercaya di Indonesia. Temukan pengalaman tak terlupakan atau buat eventmu sendiri sekarang.
                </p>
                {{-- Contoh Social Icons (Ganti href nya nanti) --}}
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-[#FF8FC7] transition">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.85.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-[#FF8FC7] transition">
                         <span class="sr-only">Twitter</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                </div>
            </div>

            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold text-gray-200 tracking-wider uppercase mb-4">Navigasi</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Beranda</a></li>
                    <li><a href="{{ route('events.index') }}" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Semua Event</a></li>
                    <li><a href="{{ route('tickets.lookup') }}" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Cek Tiket</a></li>
                </ul>
            </div>

             <div class="md:col-span-3">
                <h3 class="text-sm font-semibold text-gray-200 tracking-wider uppercase mb-4">Untuk Organizer</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('register.organizer') }}" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Daftar Jadi Organizer</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Masuk Dashboard</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Panduan Organizer</a></li>
                </ul>
            </div>

            <div class="md:col-span-3">
                 <h3 class="text-sm font-semibold text-gray-200 tracking-wider uppercase mb-4">Bantuan</h3>
                 <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-[#FF8FC7] transition">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-t border-gray-900 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-xs text-gray-600">
                    &copy; {{ date('Y') }} ngevent.id (PT Y-Tech Solutions)
                </p>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="#" class="text-xs text-gray-600 hover:text-gray-400">Terms</a>
                    <a href="#" class="text-xs text-gray-600 hover:text-gray-400">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

</div>

@stack('scripts')
</body>
</html>