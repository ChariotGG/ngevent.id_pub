<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back -->
            <nav class="text-sm mb-6">
                <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800">← Kembali ke Tiket</a>
            </nav>

            <!-- Ticket Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white text-center">
                    <p class="text-sm opacity-80">E-Ticket</p>
                    <p class="font-mono text-2xl font-bold mt-1">{{ $ticket->code }}</p>

                    @if($ticket->is_used)
                        <span class="inline-block mt-3 px-3 py-1 text-xs font-medium rounded-full bg-gray-500">
                            SUDAH DIGUNAKAN
                        </span>
                    @else
                        <span class="inline-block mt-3 px-3 py-1 text-xs font-medium rounded-full bg-green-500">
                            AKTIF
                        </span>
                    @endif
                </div>

                <!-- QR Code Placeholder -->
                <div class="p-6 text-center border-b">
                    <div class="w-48 h-48 mx-auto bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">QR Code</p>
                            <p class="text-xs text-gray-400">{{ $ticket->code }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">Tunjukkan QR code ini saat masuk venue</p>
                </div>

                <!-- Event Info -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Event</h3>
                    <p class="font-semibold text-gray-900">{{ $ticket->orderItem->order->event->title ?? 'Event' }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $ticket->orderItem->order->event->formatted_date ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $ticket->orderItem->order->event->formatted_location ?? '' }}</p>
                </div>

                <!-- Ticket Info -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Tipe Tiket</h3>
                    <p class="font-semibold text-gray-900">{{ $ticket->orderItem->ticket_name ?? 'Ticket' }}</p>
                    @if($ticket->orderItem->variant_name)
                        <p class="text-sm text-gray-600">{{ $ticket->orderItem->variant_name }}</p>
                    @endif
                </div>

                <!-- Attendee Info -->
                <div class="p-6 border-b">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Peserta</h3>
                    <p class="font-semibold text-gray-900">{{ $ticket->attendee_name }}</p>
                    <p class="text-sm text-gray-600">{{ $ticket->attendee_email }}</p>
                </div>

                <!-- Used Info -->
                @if($ticket->is_used)
                    <div class="p-6 border-b bg-gray-50">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Info Check-in</h3>
                        <p class="text-sm text-gray-600">Digunakan pada: {{ $ticket->used_at ? $ticket->used_at->format('d M Y H:i') : '-' }}</p>
                        @if($ticket->check_in_notes)
                            <p class="text-sm text-gray-600">Catatan: {{ $ticket->check_in_notes }}</p>
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="p-6">
                    <a href="{{ route('tickets.download', $ticket->code) }}"
                       class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Download PDF
                    </a>

                    <p class="text-xs text-gray-500 mt-4 text-center">
                        Order: {{ $ticket->orderItem->order->order_number ?? '' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
