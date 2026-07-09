<?php

// Application configuration. Human-readable, no abbreviations.

return [
    'name' => env('APP_NAME', 'TAVP App'),
    'env' => env('APP_ENV', 'local'),
    'debug' => env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'key' => env('APP_KEY', ''),

    // Supported environments for detection in Bootstrap.
    'environments' => ['local', 'staging', 'production'],
];
