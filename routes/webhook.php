<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
*/

Route::prefix('webhook')->name('webhook.')->group(function () {
    // Xendit Payment Webhook
    Route::post('/xendit', function () {
        // TODO: Implement XenditController
        return response()->json(['status' => 'ok']);
    })->middleware('xendit.webhook')->name('xendit');

    // Xendit Invoice Callback
    Route::post('/xendit/invoice', function () {
        // TODO: Implement XenditController
        return response()->json(['status' => 'ok']);
    })->middleware('xendit.webhook')->name('xendit.invoice');
});
