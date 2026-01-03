<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Tiket Saya</h1>

            @if($tickets->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($tickets as $ticket)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-blue-700">
                                <p class="text-white text-sm opacity-80">E-Ticket</p>
                                <p class="text-white font-mono text-lg font-bold">{{ $ticket->code }}</p>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">{{ $ticket->orderItem->order->event->title ?? 'Event' }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $ticket->orderItem->order->event->formatted_date ?? '' }}</p>
                                <p class="text-sm text-gray-600">{{ $ticket->orderItem->ticket_name ?? 'Ticket' }}</p>

                                <div class="mt-4 flex items-center justify-between">
                                    @if($ticket->is_used)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            Sudah Digunakan
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @endif
                                    <a href="{{ route('tickets.show', $ticket->code) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada tiket</h3>
                    <p class="mt-2 text-gray-500">Tiket akan muncul setelah kamu menyelesaikan pembayaran</p>
                    <a href="{{ route('events.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                        Cari Event
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
