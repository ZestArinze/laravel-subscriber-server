<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'webhook' => [
        'client_id' => env('WEBHOOK_CLIENT_ID'),
        'client_secret' => env('WEBHOOK_CLIENT_SECRET'),
    ],
];