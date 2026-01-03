<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform Configuration
    |--------------------------------------------------------------------------
    */

    // Platform fee percentage (charged to organizer)
    'platform_fee_percentage' => env('PLATFORM_FEE_PERCENTAGE', 5),

    // Payment gateway fee percentage (charged to buyer)
    'payment_fee_percentage' => env('PAYMENT_FEE_PERCENTAGE', 2.5),

    // Settlement delay in days after event completion
    'settlement_delay_days' => env('SETTLEMENT_DELAY_DAYS', 7),

    // Order expiration time in minutes
    'order_expiry_minutes' => env('ORDER_EXPIRY_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Ticket Configuration
    |--------------------------------------------------------------------------
    */

    // Maximum tickets per order
    'max_tickets_per_order' => env('MAX_TICKETS_PER_ORDER', 10),

    // Default ticket stock
    'default_ticket_stock' => env('DEFAULT_TICKET_STOCK', 100),

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    */

    'uploads' => [
        'poster' => [
            'max_size' => 2048, // KB
            'dimensions' => [
                'min_width' => 400,
                'min_height' => 600,
                'max_width' => 2000,
                'max_height' => 3000,
            ],
            'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        ],
        'banner' => [
            'max_size' => 3072, // KB
            'dimensions' => [
                'min_width' => 1200,
                'min_height' => 400,
                'max_width' => 2400,
                'max_height' => 800,
            ],
            'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        ],
        'proposal' => [
            'max_size' => 5120, // KB
            'allowed_types' => ['pdf'],
        ],
        'avatar' => [
            'max_size' => 1024, // KB
            'allowed_types' => ['jpg', 'jpeg', 'png'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'events' => 12,
        'orders' => 10,
        'attendees' => 20,
        'admin_list' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (in seconds)
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'events_list' => 600, // 10 minutes
        'event_detail' => 300, // 5 minutes
        'categories' => 3600, // 1 hour
        'featured_events' => 600, // 10 minutes
        'ticket_availability' => 60, // 1 minute
    ],

    /*
    |--------------------------------------------------------------------------
    | E-Ticket Configuration
    |--------------------------------------------------------------------------
    */

    'eticket' => [
        'code_length' => 12,
        'qr_size' => 200,
    ],
];
