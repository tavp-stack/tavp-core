<?php

// Queue configuration. Supports database and Redis drivers.

return [
    'default' => env('QUEUE_DRIVER', 'database'),

    'connections' => [
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 90,
            'max_tries' => 3,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'tavp:jobs',
            'retry_after' => 90,
            'max_tries' => 3,
            'block_for' => null,
        ],
    ],

    'batching' => [
        'database' => 'jobs',
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database'),
        'table' => 'failed_jobs',
    ],
];
