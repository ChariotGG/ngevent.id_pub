<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <!-- Success Icon -->
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h1>
                <p class="text-gray-600 mb-6">Tiket kamu sudah dikirim ke email {{ $order->customer_email }}</p>

                <!-- Order Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-gray-600 mb-2">No. Order</p>
                    <p class="font-semibold text-gray-900 mb-4">{{ $order->order_number }}</p>

                    <p class="text-sm text-gray-600 mb-2">Event</p>
                    <p class="font-semibold text-gray-900 mb-4">{{ $order->event->title }}</p>

                    <p class="text-sm text-gray-600 mb-2">Tanggal</p>
                    <p class="font-semibold text-gray-900 mb-4">{{ $order->event->formatted_date }}</p>

                    <p class="text-sm text-gray-600 mb-2">Jumlah Tiket</p>
                    <p class="font-semibold text-gray-900">{{ $order->items->sum('quantity') }} tiket</p>
                </div>

                <!-- E-Tickets -->
                @if($order->issuedTickets->count() > 0)
                    <div class="bg-blue-50 rounded-lg p-4 mb-6 text-left">
                        <h3 class="font-semibold text-blue-900 mb-3">E-Ticket Kamu</h3>
                        <div class="space-y-2">
                            @foreach($order->issuedTickets as $ticket)
                                <div class="flex items-center justify-between bg-white rounded p-3">
                                    <div>
                                        <p class="font-mono text-sm font-semibold">{{ $ticket->code }}</p>
                                        <p class="text-xs text-gray-500">{{ $ticket->orderItem->ticket_name ?? 'Ticket' }}</p>
                                    </div>
                                    <a href="{{ route('tickets.show', $ticket->code) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('tickets.index') }}"
                       class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                        Lihat Tiket Saya
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
