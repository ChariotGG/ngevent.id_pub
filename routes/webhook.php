<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Webhook Routes (Xendit Payment Callback)
|--------------------------------------------------------------------------
*/

Route::prefix('webhook')->name('webhook.')->group(function () {

    // Xendit Invoice Callback (Main)
    Route::post('/xendit/invoice', function (Request $request) {
        // Log raw payload
        \Log::channel('webhook')->info('Xendit webhook received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        // Verify webhook signature
        $callbackToken = $request->header('x-callback-token');
        $expectedToken = config('services.xendit.webhook_token');

        if (!$callbackToken || $callbackToken !== $expectedToken) {
            \Log::channel('webhook')->warning('Invalid Xendit webhook signature', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Get payment service
        $paymentService = app(\App\Services\PaymentService::class);

        try {
            $paymentService->handleWebhook($request->all());
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::channel('webhook')->error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    })->name('xendit.invoice');

    // Xendit Generic Webhook (Backup)
    Route::post('/xendit', function (Request $request) {
        return response()->json(['status' => 'ok', 'message' => 'Use /webhook/xendit/invoice instead']);
    })->name('xendit');
});
