<?php

return [
    'host' => env('REVERB_HOST', '127.0.0.1'),
    'port' => env('REVERB_PORT', 8080),
    'scheme' => env('REVERB_SCHEME', 'http'),
    'domain' => null,
    'path' => '',
    'max_request_size' => '1024',
    'capacity' => null,
    'sockets' => [
        'app_id' => env('REVERB_APP_ID'),
        'app_key' => env('REVERB_APP_KEY'),  
        'app_secret' => env('REVERB_APP_SECRET'),
    ],
    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['*'],
        'allowed_headers' => ['*'],
    ],
];
