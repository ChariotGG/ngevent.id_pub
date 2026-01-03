<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Xendit API Configuration
    |--------------------------------------------------------------------------
    */

    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),

    'secret_key' => env('XENDIT_SECRET_KEY'),

    'public_key' => env('XENDIT_PUBLIC_KEY'),

    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),

    'environment' => env('XENDIT_ENVIRONMENT', 'sandbox'), // sandbox or production

    /*
    |--------------------------------------------------------------------------
    | Invoice Configuration
    |--------------------------------------------------------------------------
    */

    // Invoice expiration time in seconds (default: 15 minutes)
    'invoice_duration' => env('XENDIT_INVOICE_DURATION', 900),

    // Currency
    'currency' => 'IDR',

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [
        'CREDIT_CARD' => true,
        'BCA' => true,
        'BNI' => true,
        'BRI' => true,
        'MANDIRI' => true,
        'PERMATA' => true,
        'BSI' => true,
        'OVO' => true,
        'DANA' => true,
        'LINKAJA' => true,
        'SHOPEEPAY' => true,
        'QRIS' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    */

    'callbacks' => [
        'success_redirect' => env('XENDIT_SUCCESS_REDIRECT', '/checkout/success'),
        'failure_redirect' => env('XENDIT_FAILURE_REDIRECT', '/checkout/failed'),
        'webhook_url' => env('XENDIT_WEBHOOK_URL', '/webhook/xendit'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Disbursement Configuration (for settlements)
    |--------------------------------------------------------------------------
    */

    'disbursement' => [
        'enabled' => env('XENDIT_DISBURSEMENT_ENABLED', false),
        'available_banks' => [
            'BCA', 'BNI', 'BRI', 'MANDIRI', 'PERMATA', 'BSI',
            'CIMB', 'DANAMON', 'BTPN', 'MAYBANK',
        ],
    ],
];
