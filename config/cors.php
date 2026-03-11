<?php

$allowedOrigins = array_values(array_filter(array_map(
    'trim',
    explode(
        ',',
        (string) env('FRONTEND_URLS', 'http://localhost:3000,http://127.0.0.1:3000,http://localhost:5173,http://127.0.0.1:5173')
    )
)));

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => $allowedOrigins,
    'allowed_origins_patterns' => [
        '#^https?://localhost(:\\d+)?$#',
        '#^https?://127\\.0\\.0\\.1(:\\d+)?$#',
    ],
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'Origin',
        'X-API-KEY',
        'X-CSRF-TOKEN',
        'X-Requested-With',
        'X-XSRF-TOKEN',
    ],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
