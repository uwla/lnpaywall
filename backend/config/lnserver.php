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
        // invoice endpoints
        'invoice' => [
            'new' => env('LNSERVER_URI') . '/api/invoices',
            'status' => env('LNSERVER_URI') . '/api/invoices/status',
        ],
        // other endpoints would go here, if necessary
    ],
];
