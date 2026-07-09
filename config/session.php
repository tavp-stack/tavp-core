<?php

// Session configuration.

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime_minutes' => 120,
    'path' => storage_path('cache/sessions'),
];
