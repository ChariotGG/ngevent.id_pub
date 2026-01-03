<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back -->
            <nav class="text-sm mb-6">
                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">← Kembali ke Pesanan</a>
            </nav>

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

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">No. Order</p>
                            <p class="text-xl font-bold text-gray-900">{{ $order->order_number }}</p>
                        </div>
                        <span class="px-4 py-2 text-sm font-medium rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </span>
                    </div>
                </div>

                <!-- Event Info -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Detail Event</h3>
                    <div class="flex gap-4">
                        <img src="{{ $order->event->poster_url }}" alt="{{ $order->event->title }}"
                             class="w-20 h-28 object-cover rounded-lg"
                             onerror="this.src='https://placehold.co/80x112?text=No+Image'">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $order->event->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $order->event->formatted_date }}</p>
                            <p class="text-sm text-gray-600">{{ $order->event->formatted_location }}</p>
                            <a href="{{ route('events.show', $order->event) }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                Lihat Event →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Data Pemesan</h3>
                    <div class="space-y-1 text-sm">
                        <p><span class="text-gray-500">Nama:</span> {{ $order->customer_name }}</p>
                        <p><span class="text-gray-500">Email:</span> {{ $order->customer_email }}</p>
                        <p><span class="text-gray-500">No. HP:</span> {{ $order->customer_phone }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Detail Tiket</h3>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->ticket_name }}</p>
                                    @if($item->variant_name)
                                        <p class="text-sm text-gray-500">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <p class="font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Ringkasan Pembayaran</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Biaya Layanan</span>
                            <span>Rp {{ number_format($order->payment_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- E-Tickets (if paid) -->
                @if($status === 'paid' && $order->issuedTickets->count() > 0)
                    <div class="p-6 border-b bg-blue-50">
                        <h3 class="text-sm font-medium text-blue-800 mb-3">E-Ticket</h3>
                        <div class="space-y-2">
                            @foreach($order->issuedTickets as $ticket)
                                <div class="flex items-center justify-between bg-white rounded-lg p-3">
                                    <div>
                                        <p class="font-mono text-sm font-semibold">{{ $ticket->code }}</p>
                                        <p class="text-xs text-gray-500">{{ $ticket->orderItem->ticket_name ?? 'Ticket' }}</p>
                                    </div>
                                    <a href="{{ route('tickets.show', $ticket->code) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat Tiket →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="p-6">
                    @if($status === 'pending')
                        @php
                            $isExpired = $order->expires_at && $order->expires_at->isPast();
                        @endphp

                        @if(!$isExpired)
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('checkout.payment', $order) }}"
                                class="flex-1 bg-blue-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Bayar Sekarang
                                </a>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin membatalkan pesanan ini?')"
                                            class="w-full border border-red-500 text-red-500 py-3 px-4 rounded-lg font-semibold hover:bg-red-50 transition">
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-red-600 font-medium mb-4">Waktu pembayaran telah habis</p>
                                <a href="{{ route('checkout.index', $order->event) }}"
                                class="inline-block bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Pesan Ulang
                                </a>
                            </div>
                        @endif
                    @elseif($status === 'paid')
                        <a href="{{ route('tickets.index') }}"
                        class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Lihat Semua Tiket Saya
                        </a>
                    @endif

                    <p class="text-xs text-gray-500 mt-4 text-center">
                        Tanggal Order: {{ $order->created_at->format('d M Y H:i') }}
                        @if($order->paid_at)
                            <br>Tanggal Bayar: {{ $order->paid_at->format('d M Y H:i') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
