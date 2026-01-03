<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Pesanan Saya</h1>

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        @php
                            $status = $order->status->value ?? $order->status;
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'awaiting_payment' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'expired' => 'bg-gray-100 text-gray-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'refunded' => 'bg-purple-100 text-purple-800',
                            ];
                            $statusLabels = [
                                'pending' => 'Menunggu Pembayaran',
                                'awaiting_payment' => 'Menunggu Pembayaran',
                                'paid' => 'Lunas',
                                'completed' => 'Selesai',
                                'expired' => 'Kadaluarsa',
                                'cancelled' => 'Dibatalkan',
                                'refunded' => 'Refund',
                            ];
                        @endphp
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex gap-4">
                                    <img src="{{ $order->event->poster_url }}" alt="{{ $order->event->title }}"
                                         class="w-16 h-20 object-cover rounded-lg"
                                         onerror="this.src='https://placehold.co/64x80?text=No+Image'">
                                    <div>
                                        <p class="text-sm text-gray-500">{{ $order->order_number }}</p>
                                        <h3 class="font-semibold text-gray-900">{{ $order->event->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $order->event->formatted_date }}</p>
                                        <p class="text-sm text-gray-600">{{ $order->items->sum('quantity') }} tiket</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>

                                    <span class="inline-block mt-2 px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                                    </span>

                                    <div class="mt-3">
                                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Lihat Detail →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada pesanan</h3>
                    <p class="mt-2 text-gray-500">Mulai cari event seru dan pesan tiketnya!</p>
                    <a href="{{ route('events.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                        Cari Event
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
