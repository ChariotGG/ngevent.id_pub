<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Timer -->
                @if($order->expires_at)
                <div class="text-center mb-8" x-data="countdownTimer()" x-init="init('{{ $order->expires_at->toIso8601String() }}')">
                    <p class="text-gray-600 mb-2">Selesaikan pembayaran dalam</p>
                    <div class="text-3xl font-bold" :class="expired ? 'text-red-600' : 'text-red-600'" x-text="display">--:--</div>
                    <p class="text-sm text-gray-500 mt-2">Order akan otomatis dibatalkan jika tidak dibayar</p>
                </div>
                @endif

                <!-- Order Details -->
                <div class="border-t border-b py-6 mb-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Detail Pesanan</h2>

                    <div class="flex gap-4 mb-4">
                        <img src="{{ $order->event->poster_url }}" alt="{{ $order->event->title }}"
                             class="w-16 h-20 object-cover rounded-lg"
                             onerror="this.src='https://placehold.co/64x80?text=No+Image'">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $order->event->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $order->event->formatted_date }}</p>
                            <p class="text-sm text-gray-600">{{ $order->event->formatted_location }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <p class="text-gray-600">No. Order: <span class="font-medium text-gray-900">{{ $order->order_number }}</span></p>
                        @foreach($order->items as $item)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $item->ticket_name }} x{{ $item->quantity }}</span>
                                <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="space-y-2 text-sm mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon</span>
                            <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Layanan</span>
                        <span class="font-medium">Rp {{ number_format($order->payment_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span>Total Pembayaran</span>
                        <span class="text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Payment Button (Demo) -->
                <form action="{{ route('checkout.process', $order) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-4 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Bayar Sekarang (Demo)
                    </button>
                </form>

                <p class="text-xs text-gray-500 mt-4 text-center">
                    * Ini adalah mode demo. Klik tombol untuk simulasi pembayaran berhasil.
                </p>

                <!-- Cancel -->
                <div class="text-center mt-6">
                    <a href="{{ route('events.show', $order->event) }}" class="text-gray-600 hover:text-gray-800 text-sm">
                        Batalkan & Kembali ke Event
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function countdownTimer() {
            return {
                display: '--:--',
                expired: false,
                interval: null,
                init(expiresAt) {
                    const endTime = new Date(expiresAt).getTime();

                    this.updateDisplay(endTime);

                    this.interval = setInterval(() => {
                        this.updateDisplay(endTime);
                    }, 1000);
                },
                updateDisplay(endTime) {
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance < 0) {
                        clearInterval(this.interval);
                        this.display = '00:00';
                        this.expired = true;
                        // Redirect ke expired page
                        window.location.href = '{{ route("checkout.expired", $order) }}';
                        return;
                    }

                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    this.display = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                }
            }
        }
    </script>
</x-app-layout>
