<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class InsufficientTicketException extends Exception
{
    public function __construct(
        public int $ticketVariantId,
        public int $requested,
        public int $available,
        string $message = null
    ) {
        parent::__construct(
            $message ?? "Stok tiket tidak mencukupi. Diminta: {$requested}, Tersedia: {$available}"
        );
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'insufficient_stock',
                'message' => 'Tiket tidak tersedia dalam jumlah yang diminta',
                'details' => [
                    'ticket_variant_id' => $this->ticketVariantId,
                    'requested' => $this->requested,
                    'available' => $this->available,
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return back()->withErrors([
            'ticket' => 'Tiket tidak tersedia dalam jumlah yang diminta. Tersedia: ' . $this->available
        ])->withInput();
    }

    public function report(): bool
    {
        // Don't report to error tracking - this is expected user behavior
        return false;
    }
}
