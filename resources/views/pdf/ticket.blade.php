<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket {{ $ticket->code }}</title>
    <style>
        @page {
            margin: 15px;
            size: A5 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            background: #fff;
        }
        .ticket {
            width: 100%;
            border: 2px solid #2563eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .ticket-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            text-align: center;
        }
        .ticket-header h1 {
            font-size: 18px;
            margin: 0;
        }
        .ticket-header .brand {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 2px;
        }
        .ticket-body {
            display: table;
            width: 100%;
        }
        .ticket-info {
            display: table-cell;
            width: 62%;
            padding: 12px 15px;
            vertical-align: top;
        }
        .ticket-qr {
            display: table-cell;
            width: 38%;
            padding: 12px 15px;
            text-align: center;
            vertical-align: top;
            border-left: 2px dashed #d1d5db;
            background: #f9fafb;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }
        .info-value {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
        }
        .info-value.large {
            font-size: 14px;
        }
        .ticket-code-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .ticket-code-box {
            width: 130px;
            padding: 15px 10px;
            background: #fff;
            border: 2px solid #2563eb;
            margin: 0 auto 8px;
            border-radius: 4px;
        }
        .ticket-code {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 1px;
            word-break: break-all;
        }
        .divider {
            border-top: 1px dashed #d1d5db;
            margin: 8px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-used {
            background: #fee2e2;
            color: #991b1b;
        }
        .ticket-footer {
            background: #f3f4f6;
            padding: 8px 15px;
            border-top: 1px solid #e5e7eb;
        }
        .footer-table {
            width: 100%;
        }
        .footer-left {
            text-align: left;
        }
        .footer-right {
            text-align: right;
        }
        .footer-label {
            font-size: 7px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .footer-value {
            font-size: 9px;
            color: #374151;
            font-weight: bold;
        }
        .instructions {
            margin-top: 6px;
            padding: 6px 10px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            font-size: 8px;
            color: #92400e;
        }
        .instructions strong {
            color: #78350f;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="ticket-header">
            <div class="brand">ngevent.id</div>
            <h1>E-TICKET</h1>
        </div>

        <!-- Body -->
        <div class="ticket-body">
            <!-- Info Section -->
            <div class="ticket-info">
                <div class="info-row">
                    <div class="info-label">Event</div>
                    <div class="info-value large">{{ $ticket->orderItem->order->event->title ?? 'Event' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tanggal & Waktu</div>
                    <div class="info-value">{{ $ticket->orderItem->order->event->formatted_date ?? '-' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Lokasi</div>
                    <div class="info-value">{{ $ticket->orderItem->order->event->formatted_location ?? '-' }}</div>
                </div>

                <div class="divider"></div>

                <div class="info-row">
                    <div class="info-label">Tipe Tiket</div>
                    <div class="info-value">
                        {{ $ticket->orderItem->ticket_name ?? 'Ticket' }}
                        @if($ticket->orderItem->variant_name)
                            - {{ $ticket->orderItem->variant_name }}
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Nama Peserta</div>
                    <div class="info-value">{{ $ticket->attendee_name }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $ticket->attendee_email }}</div>
                </div>
            </div>

            <!-- Code Section -->
            <div class="ticket-qr">
                <div class="ticket-code-label">Kode Tiket</div>
                <div class="ticket-code-box">
                    <div class="ticket-code">{{ $ticket->code }}</div>
                </div>

                @php
                    $statusText = $ticket->is_used ? 'SUDAH DIGUNAKAN' : 'BELUM DIGUNAKAN';
                    $statusClass = $ticket->is_used ? 'status-used' : 'status-active';
                @endphp
                <div class="status-badge {{ $statusClass }}">
                    {{ $statusText }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        <div class="footer-label">No. Order</div>
                        <div class="footer-value">{{ $ticket->orderItem->order->order_number ?? '-' }}</div>
                    </td>
                    <td class="footer-right">
                        <div class="footer-label">Tanggal Pembelian</div>
                        <div class="footer-value">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
                    </td>
                </tr>
            </table>

            <div class="instructions">
                <strong>Petunjuk:</strong> Tunjukkan e-ticket ini saat memasuki venue. Sebutkan kode tiket kepada petugas untuk verifikasi. Tiket hanya berlaku untuk 1 orang.
            </div>
        </div>
    </div>
</body>
</html>
