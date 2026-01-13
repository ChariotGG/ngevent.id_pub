<x-organizer-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Buat Event Baru</h1>
            <p class="text-sm text-gray-600 mt-1">Isi informasi event Anda. Pastikan semua data akurat sebelum publish.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Ada beberapa kesalahan:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Informasi Dasar --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Event *</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            placeholder="Contoh: Java Jazz Festival 2026" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Event * (min. 100 karakter)</label>
                        <textarea name="description" rows="6"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            placeholder="Jelaskan event Anda secara detail..." required>{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Tips: Jelaskan apa yang menarik dari event Anda, siapa yang akan tampil, dan apa yang didapatkan pengunjung.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poster Event</label>
                        <input type="file" name="poster" accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Rekomendasi ukuran: 1080x1350px</p>
                    </div>
                </div>
            </div>

            {{-- Tanggal & Waktu --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tanggal & Waktu</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai *</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai *</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Lokasi</h2>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_online" id="is_online" value="1" {{ old('is_online') ? 'checked' : '' }}
                            class="w-4 h-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                        <label for="is_online" class="ml-2 text-sm text-gray-700">Event Online</label>
                    </div>

                    <div id="online-url-field" style="{{ old('is_online') ? '' : 'display:none;' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Online Event</label>
                        <input type="url" name="online_url" value="{{ old('online_url') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            placeholder="https://zoom.us/j/123456789">
                    </div>

                    <div id="venue-fields" style="{{ old('is_online') ? 'display:none;' : '' }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Venue *</label>
                                <input type="text" name="venue_name" value="{{ old('venue_name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    placeholder="Contoh: Jakarta Convention Center">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap *</label>
                                <textarea name="venue_address" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    placeholder="Jalan, nomor, gedung">{{ old('venue_address') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota *</label>
                                    <input type="text" name="city" value="{{ old('city') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                        placeholder="Jakarta">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi *</label>
                                    <input type="text" name="province" value="{{ old('province') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                        placeholder="DKI Jakarta">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tiket --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Tiket</h2>
                    <button type="button" id="add-ticket-btn" class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                        + Tambah Tiket
                    </button>
                </div>

                <div id="tickets-container" class="space-y-4">
                    <div class="ticket-item border border-gray-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tiket *</label>
                                <input type="text" name="tickets[0][name]" value="{{ old('tickets.0.name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    placeholder="Contoh: Regular" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                                <input type="number" name="tickets[0][price]" value="{{ old('tickets.0.price', 0) }}" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    placeholder="0" required>
                                <p class="text-xs text-gray-500 mt-1">Isi 0 untuk tiket gratis</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                                <input type="number" name="tickets[0][stock]" value="{{ old('tickets.0.stock') }}" min="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    placeholder="100" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('organizer.events.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-pink-600 text-white font-medium rounded-lg hover:bg-pink-700 focus:ring-4 focus:ring-pink-200">
                    Simpan Event
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Toggle online event
        document.getElementById('is_online').addEventListener('change', function() {
            const onlineField = document.getElementById('online-url-field');
            const venueFields = document.getElementById('venue-fields');

            if (this.checked) {
                onlineField.style.display = 'block';
                venueFields.style.display = 'none';
            } else {
                onlineField.style.display = 'none';
                venueFields.style.display = 'block';
            }
        });

        // Add ticket dynamic
        let ticketIndex = 1;
        document.getElementById('add-ticket-btn').addEventListener('click', function() {
            const container = document.getElementById('tickets-container');
            const ticketHtml = `
                <div class="ticket-item border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-sm font-medium text-gray-700">Tiket ${ticketIndex + 1}</span>
                        <button type="button" class="remove-ticket text-sm text-red-600 hover:text-red-700">Hapus</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tiket *</label>
                            <input type="text" name="tickets[${ticketIndex}][name]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="Contoh: VIP" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                            <input type="number" name="tickets[${ticketIndex}][price]" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="0" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                            <input type="number" name="tickets[${ticketIndex}][stock]" min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="100" required>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', ticketHtml);
            ticketIndex++;
        });

        // Remove ticket
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-ticket')) {
                const ticketItem = e.target.closest('.ticket-item');
                if (document.querySelectorAll('.ticket-item').length > 1) {
                    ticketItem.remove();
                } else {
                    alert('Minimal harus ada 1 tiket');
                }
            }
        });
    </script>
    @endpush
</x-organizer-layout>
