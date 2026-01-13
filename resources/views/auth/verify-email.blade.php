<x-app-layout>
    {{-- Auto redirect jika sudah verified --}}
    @if(auth()->user()->hasVerifiedEmail())
        <script>
            window.location.href = "{{ route('organizer.dashboard') }}";
        </script>
    @endif

    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Verifikasi Email Anda</h1>
            <p class="text-sm text-gray-400 mb-6">
                Kami telah mengirimkan link verifikasi ke email <strong class="text-white">{{ auth()->user()->email }}</strong>.
                Silakan cek inbox Anda dan klik link untuk verifikasi.
            </p>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                    <p class="text-sm text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2.5 border border-gray-700 text-gray-300 font-medium rounded-lg hover:bg-gray-800 transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <p class="text-xs text-gray-500 mt-4">
                Tidak menerima email? Cek folder spam atau kirim ulang di atas.
            </p>

            <div class="mt-6 pt-6 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-white transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
