<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'ngevent.id') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-black text-white">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4">
        <!-- Logo -->
        <div class="mb-8">
            <a href="{{ route('home') }}">
                <img src="{{ asset('image/ngevent.id_logo.png') }}" alt="ngevent.id" class="h-10">
            </a>
        </div>

        <!-- Content -->
        {{ $slot }}

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-600">
                &copy; {{ date('Y') }} ngevent.id. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
