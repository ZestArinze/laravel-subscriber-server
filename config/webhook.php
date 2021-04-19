<?php

return [
    'publisher_api_base_url'        => env('PUBLISHER_API_BASE_URL', 'http://127.0.0.1:9000/api'),
    'publisher_callback_url_path'   => '/api/webhooks/posts',
    'publisher_client_id'           => env('PUBLISHER_CLIENT_ID'),
    'publisher_client_secret'       => env('PUBLISHER_CLIENT_SECRET'),
];