<?php

// Database configuration. Supports MySQL, PostgreSQL, SQLite.

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'adapter' => 'Mysql',
            'host' => env('DB_HOST', 'database'),
            'port' => env('DB_PORT', 3306),
            'dbname' => env('DB_DATABASE', 'tavp'),
            'username' => env('DB_USERNAME', 'tavp'),
            'password' => env('DB_PASSWORD', 'tavp'),
            'charset' => 'utf8mb4',
        ],
        'pgsql' => [
            'adapter' => 'Postgresql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 5432),
            'dbname' => env('DB_DATABASE', 'tavp'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
        ],
        'sqlite' => [
            'adapter' => 'Sqlite',
            'dbname' => env('DB_DATABASE', 'storage/tavp.sqlite'),
        ],
    ],
];
