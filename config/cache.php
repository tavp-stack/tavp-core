<?php

// Cache configuration.

return [
    'driver' => env('CACHE_DRIVER', 'file'),
    'path' => storage_path('cache'),
    'redis' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
    ],
];
