<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentFailedException extends Exception
{
    public function __construct(
        public ?int $orderId = null,
        public ?string $reason = null,
        string $message = null
    ) {
        parent::__construct($message ?? 'Pembayaran gagal diproses');
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'payment_failed',
                'message' => $this->getMessage(),
                'reason' => $this->reason,
            ], Response::HTTP_PAYMENT_REQUIRED);
        }

        if ($this->orderId) {
            return redirect()
                ->route('checkout.failed', ['order' => $this->orderId])
                ->with('error', $this->getMessage());
        }

        return back()->withErrors([
            'payment' => $this->getMessage()
        ]);
    }

    public function report(): void
    {
        // Log payment failures for monitoring
        logger()->channel('payment')->error('Payment failed', [
            'order_id' => $this->orderId,
            'reason' => $this->reason,
            'message' => $this->getMessage(),
        ]);
    }
}
