<?php

$connections = [];
if (ENV('DB_HOST_LOCAL')) {
    $connections['local'] = [
        'driver' => 'pgsql',
        'host' => env('DB_HOST_LOCAL'),
        'port' => env('DB_PORT_LOCAL',),
        'database' => env('DB_DATABASE_LOCAL'),
        'username' => env('DB_USERNAME_LOCAL'),
        'password' => env('DB_PASSWORD_LOCAL'),
    ];
}

if (ENV('DB_HOST_STAGING')) {
    $connections['staging'] = [
        'driver' => 'pgsql',
        'host' => env('DB_HOST_STAGING'),
        'port' => env('DB_PORT_STAGING',),
        'database' => env('DB_DATABASE_STAGING'),
        'username' => env('DB_USERNAME_STAGING'),
        'password' => env('DB_PASSWORD_STAGING'),
    ];
}

if (ENV('DB_HOST_PRODUCTION')) {
    $connections['production'] = [
        'driver' => 'pgsql',
        'host' => env('DB_HOST_PRODUCTION'),
        'port' => env('DB_PORT_PRODUCTION',),
        'database' => env('DB_DATABASE_PRODUCTION'),
        'username' => env('DB_USERNAME_PRODUCTION'),
        'password' => env('DB_PASSWORD_PRODUCTION'),
    ];
}

return [
    'default' => 'local',
    'connections' => $connections
];
