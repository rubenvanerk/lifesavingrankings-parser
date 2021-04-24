<?php

return [
    'default' => env('DB_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'driver' => env('DB_DRIVER'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true,
            ],
        ],
        'rankings' => [
            'driver' => env('RANKINGS_DB_DRIVER'),
            'host' => env('RANKINGS_DB_HOST'),
            'port' => env('RANKINGS_DB_PORT'),
            'database' => env('RANKINGS_DB_DATABASE'),
            'username' => env('RANKINGS_DB_USERNAME'),
            'password' => env('RANKINGS_DB_PASSWORD'),
        ],
    ],

    'migrations' => 'migrations',

];
