<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <!-- Failed Icon -->
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Gagal</h1>
                <p class="text-gray-600 mb-6">
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        Maaf, pembayaran kamu tidak dapat diproses. Silakan coba lagi.
                    @endif
                </p>

                <!-- Order Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-gray-600 mb-2">No. Order</p>
                    <p class="font-semibold text-gray-900 mb-4">{{ $order->order_number }}</p>

                    <p class="text-sm text-gray-600 mb-2">Event</p>
                    <p class="font-semibold text-gray-900">{{ $order->event->title }}</p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('checkout.payment', $order) }}"
                       class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                        Coba Lagi
                    </a>
                    <a href="{{ route('events.show', $order->event) }}"
                       class="flex-1 border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                        Kembali ke Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
