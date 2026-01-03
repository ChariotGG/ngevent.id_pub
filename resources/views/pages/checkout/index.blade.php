<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8" x-data="checkoutForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="text-sm mb-6">
                <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-800">← Kembali ke Event</a>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Ticket Selection -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-6">Pilih Tiket</h1>

                        <form action="{{ route('checkout.store', $event) }}" method="POST" id="checkout-form">
                            @csrf

                            @if(session('error'))
                                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <!-- Tickets -->
                            <div class="space-y-4 mb-8">
                                @foreach($event->tickets as $ticket)
                                    @foreach($ticket->variants as $variant)
                                        @php
                                            $available = $variant->stock - $variant->sold_count - $variant->reserved_count;
                                        @endphp
                                        <div class="border border-gray-200 rounded-lg p-4 @if($available <= 0) opacity-50 @endif">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <h3 class="font-semibold text-gray-900">
                                                        {{ $ticket->name }}
                                                        @if($variant->name)
                                                            <span class="text-gray-500">- {{ $variant->name }}</span>
                                                        @endif
                                                    </h3>
                                                    @if($ticket->description)
                                                        <p class="text-sm text-gray-600 mt-1">{{ $ticket->description }}</p>
                                                    @endif
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        Tersedia: {{ $available }} tiket
                                                    </p>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="text-lg font-bold text-blue-600">
                                                        @if($variant->price == 0)
                                                            Gratis
                                                        @else
                                                            Rp {{ number_format($variant->price, 0, ',', '.') }}
                                                        @endif
                                                    </p>

                                                    @if($available > 0)
                                                        <div class="flex items-center justify-end mt-2 space-x-2">
                                                            <button type="button"
                                                                    @click="decreaseQty({{ $variant->id }})"
                                                                    class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                                                -
                                                            </button>
                                                            <input type="number"
                                                                   name="tickets[{{ $variant->id }}][quantity]"
                                                                   x-model.number="quantities[{{ $variant->id }}]"
                                                                   min="0"
                                                                   max="{{ min($available, 10) }}"
                                                                   class="w-16 text-center border border-gray-300 rounded-lg py-1"
                                                                   readonly>
                                                            <input type="hidden" name="tickets[{{ $variant->id }}][variant_id]" value="{{ $variant->id }}">
                                                            <button type="button"
                                                                    @click="increaseQty({{ $variant->id }}, {{ min($available, 10) }})"
                                                                    class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                                                +
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-red-500 text-sm">Habis</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>

                            <!-- Customer Info -->
                            <div class="border-t pt-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Data Pemesan</h2>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                        <input type="text" name="customer_name" required
                                               value="{{ old('customer_name', auth()->user()->name) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('customer_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="customer_email" required
                                               value="{{ old('customer_email', auth()->user()->email) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('customer_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP/WhatsApp</label>
                                        <input type="tel" name="customer_phone" required
                                               value="{{ old('customer_phone', auth()->user()->phone) }}"
                                               placeholder="08xxxxxxxxxx"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('customer_phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Voucher -->
                            <div class="border-t pt-6 mt-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Kode Voucher</h2>
                                <div class="flex gap-2">
                                    <input type="text" name="voucher_code"
                                           placeholder="Masukkan kode voucher"
                                           value="{{ old('voucher_code') }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Voucher akan divalidasi saat checkout</p>
                            </div>

                            <!-- Submit Button (Mobile) -->
                            <div class="mt-8 lg:hidden">
                                <button type="submit"
                                        :disabled="totalQty === 0"
                                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    Lanjut ke Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Order Summary (TANPA x-data sendiri) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                        <!-- Event Info -->
                        <div class="flex gap-4 mb-6 pb-6 border-b">
                            <img src="{{ $event->poster_url }}" alt="{{ $event->title }}"
                                 class="w-20 h-28 object-cover rounded-lg"
                                 onerror="this.src='https://placehold.co/80x112?text=No+Image'">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $event->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $event->formatted_date }}</p>
                                <p class="text-sm text-gray-600">{{ $event->formatted_location }}</p>
                            </div>
                        </div>

                        <!-- Summary -->
                        <h2 class="font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah Tiket</span>
                                <span class="font-medium" x-text="totalQty + ' tiket'">0 tiket</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium" x-text="formatRupiah(subtotal)">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Biaya Layanan (2.5%)</span>
                                <span class="font-medium" x-text="formatRupiah(serviceFee)">Rp 0</span>
                            </div>
                        </div>

                        <div class="border-t mt-4 pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-blue-600" x-text="formatRupiah(total)">Rp 0</span>
                            </div>
                        </div>

                        <!-- Submit Button (Desktop) -->
                        <button type="submit"
                                form="checkout-form"
                                :disabled="totalQty === 0"
                                class="hidden lg:block w-full mt-6 bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                            Lanjut ke Pembayaran
                        </button>

                        <p class="text-xs text-gray-500 mt-4 text-center">
                            Dengan melanjutkan, kamu menyetujui Syarat & Ketentuan kami
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function checkoutForm() {
            return {
                quantities: {
                    @foreach($event->tickets as $ticket)
                        @foreach($ticket->variants as $variant)
                            {{ $variant->id }}: 0,
                        @endforeach
                    @endforeach
                },
                prices: {
                    @foreach($event->tickets as $ticket)
                        @foreach($ticket->variants as $variant)
                            {{ $variant->id }}: {{ $variant->price }},
                        @endforeach
                    @endforeach
                },
                get totalQty() {
                    return Object.values(this.quantities).reduce((a, b) => a + b, 0);
                },
                get subtotal() {
                    let total = 0;
                    for (let id in this.quantities) {
                        total += this.quantities[id] * this.prices[id];
                    }
                    return total;
                },
                get serviceFee() {
                    return Math.ceil(this.subtotal * 0.025);
                },
                get total() {
                    return this.subtotal + this.serviceFee;
                },
                increaseQty(id, max) {
                    if (this.quantities[id] < max) {
                        this.quantities[id]++;
                    }
                },
                decreaseQty(id) {
                    if (this.quantities[id] > 0) {
                        this.quantities[id]--;
                    }
                },
                formatRupiah(amount) {
                    return 'Rp ' + amount.toLocaleString('id-ID');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
