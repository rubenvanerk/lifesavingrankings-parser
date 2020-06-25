<?php

return [

    'default' => env('FILESYSTEM_DRIVER', 's3'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('S3_AWS_ACCESS_KEY_ID'),
            'secret' => env('S3_AWS_SECRET_ACCESS_KEY'),
            'region' => env('S3_AWS_DEFAULT_REGION'),
            'bucket' => env('S3_AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],

    ],

];
