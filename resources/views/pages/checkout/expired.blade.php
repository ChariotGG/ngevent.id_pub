<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <!-- Expired Icon -->
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Waktu Pembayaran Habis</h1>
                <p class="text-gray-600 mb-6">Order kamu telah dibatalkan karena tidak dibayar dalam waktu yang ditentukan.</p>

                <!-- Order Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-gray-600 mb-2">No. Order</p>
                    <p class="font-semibold text-gray-900 mb-4">{{ $order->order_number }}</p>

                    <p class="text-sm text-gray-600 mb-2">Event</p>
                    <p class="font-semibold text-gray-900">{{ $order->event->title }}</p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('checkout.index', $order->event) }}"
                       class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                        Pesan Ulang
                    </a>
                    <a href="{{ route('events.index') }}"
                       class="flex-1 border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                        Cari Event Lain
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
