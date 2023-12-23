<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Endpoints
    |--------------------------------------------------------------------------
    |
    | Endpoints for the LN server.
    |
    */
    'endpoint' => [
        // Invoice endpoints
        'invoice' => [
            'new' => env('LNSERVER_URI') . '/api/invoices',
            'status' => env('LNSERVER_URI') . '/api/invoices/status',
        ],

        // Other endpoints should go here, if necessary.
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    |
    | Payment configuration.
    |
    */
    'payment' => [
        'satoshis_per_second' => env('LNPAY_SATOSHIS_PER_SECOND', 1),
        'min_seconds' => env('LNPAY_MIN_SECONDS', 300),
    ]
];
