<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\PaymentFailedException;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Xendit\XenditClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private XenditClient $xenditClient,
        private OrderService $orderService,
    ) {}

    public function createInvoice(Order $order): Payment
    {
        try {
            $externalId = "order-{$order->order_number}";

            $items = $order->items->map(fn($item) => [
                'name' => $item->display_name,
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
            ])->toArray();

            // Add fees as line items
            if ($order->payment_fee > 0) {
                $items[] = [
                    'name' => 'Biaya Pembayaran',
                    'quantity' => 1,
                    'price' => $order->payment_fee,
                ];
            }

            $response = $this->xenditClient->createInvoice([
                'external_id' => $externalId,
                'amount' => $order->total,
                'payer_email' => $order->customer_email,
                'description' => "Pembayaran tiket {$order->event->title}",
                'invoice_duration' => config('xendit.invoice_duration', 900),
                'customer' => [
                    'given_names' => $order->customer_name,
                    'email' => $order->customer_email,
                    'mobile_number' => $order->customer_phone,
                ],
                'items' => $items,
                'success_redirect_url' => route('checkout.success', $order),
                'failure_redirect_url' => route('checkout.failed', $order),
                'currency' => 'IDR',
            ]);

            return Payment::create([
                'order_id' => $order->id,
                'xendit_invoice_id' => $response['id'],
                'xendit_invoice_url' => $response['invoice_url'],
                'xendit_external_id' => $externalId,
                'amount' => $order->total,
                'status' => PaymentStatus::PENDING,
                'expires_at' => now()->addSeconds(config('xendit.invoice_duration', 900)),
                'raw_response' => $response,
            ]);

        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to create invoice', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new PaymentFailedException(
                $order->id,
                'invoice_creation_failed',
                'Gagal membuat invoice pembayaran'
            );
        }
    }

    public function handleWebhook(array $payload): void
    {
        $invoiceId = $payload['id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$invoiceId || !$status) {
            Log::channel('webhook')->warning('Invalid webhook payload', $payload);
            return;
        }

        $payment = Payment::where('xendit_invoice_id', $invoiceId)->first();

        if (!$payment) {
            Log::channel('webhook')->warning('Payment not found for invoice', ['invoice_id' => $invoiceId]);
            return;
        }

        // Idempotency check
        if ($payment->status->isFinal()) {
            Log::channel('webhook')->info('Payment already in final state', [
                'invoice_id' => $invoiceId,
                'status' => $payment->status->value,
            ]);
            return;
        }

        DB::transaction(function () use ($payment, $payload, $status) {
            match ($status) {
                'PAID', 'SETTLED' => $this->handlePaid($payment, $payload),
                'EXPIRED' => $this->handleExpired($payment),
                default => Log::channel('webhook')->info('Unhandled status', ['status' => $status]),
            };
        });
    }

    protected function handlePaid(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
            'payment_method' => $payload['payment_method'] ?? null,
            'payment_channel' => $payload['payment_channel'] ?? null,
            'paid_amount' => $payload['paid_amount'] ?? null,
            'adjusted_received_amount' => $payload['adjusted_received_amount'] ?? null,
            'fees_paid_amount' => $payload['fees_paid_amount'] ?? null,
            'raw_response' => $payload,
        ]);

        // Mark order as paid
        app(OrderService::class)->markAsPaid($payment->order);

        Log::channel('payment')->info('Payment successful', [
            'order_id' => $payment->order_id,
            'invoice_id' => $payment->xendit_invoice_id,
            'amount' => $payment->amount,
        ]);
    }

    protected function handleExpired(Payment $payment): void
    {
        $payment->update(['status' => PaymentStatus::EXPIRED]);

        // Expire the order
        app(OrderService::class)->expire($payment->order);

        Log::channel('payment')->info('Payment expired', [
            'order_id' => $payment->order_id,
            'invoice_id' => $payment->xendit_invoice_id,
        ]);
    }

    public function getPaymentStatus(Order $order): ?Payment
    {
        return $order->payment;
    }

    public function refreshPaymentStatus(Payment $payment): Payment
    {
        try {
            $response = $this->xenditClient->getInvoice($payment->xendit_invoice_id);

            $status = match ($response['status']) {
                'PAID', 'SETTLED' => PaymentStatus::PAID,
                'EXPIRED' => PaymentStatus::EXPIRED,
                'PENDING' => PaymentStatus::PENDING,
                default => $payment->status,
            };

            if ($status !== $payment->status) {
                $payment->update([
                    'status' => $status,
                    'raw_response' => $response,
                ]);

                if ($status === PaymentStatus::PAID) {
                    $this->handlePaid($payment, $response);
                } elseif ($status === PaymentStatus::EXPIRED) {
                    $this->handleExpired($payment);
                }
            }

            return $payment->fresh();

        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to refresh payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return $payment;
        }
    }

    public function expireInvoice(Payment $payment): bool
    {
        try {
            $this->xenditClient->expireInvoice($payment->xendit_invoice_id);
            $this->handleExpired($payment);
            return true;
        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to expire invoice', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
