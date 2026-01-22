<x-app-layout>
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Daftar Sebagai Organizer</h2>
                <p class="mt-2 text-gray-600">Mulai kelola event dan jual tiket di ngevent.id</p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('register.organizer') }}" method="POST">
                @csrf

                <!-- Organizer Name -->
                <div class="p-4 bg-blue-50 rounded-lg">
                    <label for="organizer_name" class="block text-sm font-medium text-blue-800 mb-1">Nama Organizer / Brand</label>
                    <input id="organizer_name" name="organizer_name" type="text" value="{{ old('organizer_name') }}" required
                           class="w-full px-4 py-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('organizer_name') border-red-500 @enderror"
                           placeholder="Nama event organizer atau brand kamu">
                    @error('organizer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (PIC)</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Nama penanggung jawab">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           placeholder="email@organizer.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ulangi password">
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input type="checkbox" name="terms" required class="h-4 w-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">
                        Saya setuju dengan <a href="#" class="text-blue-600 hover:underline">Syarat & Ketentuan Organizer</a>
                        dan <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition">
                    Daftar Sebagai Organizer
                </button>
            </form>

            <!-- Back to regular register -->
            <div class="text-center">
                <p class="text-gray-600">Hanya ingin membeli tiket?</p>
                <a href="{{ route('register') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800 font-medium">
                    ← Beli Tiket
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
