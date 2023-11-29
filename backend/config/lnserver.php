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
            'new' => 'lnserver:8080/api/invoices',
            'status' => 'lnserver:8080/api/invoices/status',
        ],
        // other endpoints would go here, if necessary
    ],
];
