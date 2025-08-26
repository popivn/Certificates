<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Environment Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration specific to production environment.
    | These settings will override the default configuration when
    | APP_ENV=production is set in your .env file.
    |
    */

    'force_https' => true,
    'secure_headers' => true,
    
    'session' => [
        'secure' => true,
        'same_site' => 'strict',
        'http_only' => true,
    ],
    
    'mail' => [
        'mailers' => [
            'smtp' => [
                'scheme' => 'tls',
            ],
        ],
    ],
    
    'cache' => [
        'default' => 'redis',
        'stores' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'cache',
            ],
        ],
    ],
    
    'queue' => [
        'default' => 'redis',
        'connections' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'queue' => env('REDIS_QUEUE', 'default'),
                'retry_after' => 90,
                'block_for' => null,
            ],
        ],
    ],
    
    'logging' => [
        'default' => 'stack',
        'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['daily', 'slack'],
                'ignore_exceptions' => false,
            ],
            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/laravel.log'),
                'level' => env('LOG_LEVEL', 'error'),
                'days' => 14,
            ],
            'slack' => [
                'driver' => 'slack',
                'url' => env('LOG_SLACK_WEBHOOK_URL'),
                'username' => 'Laravel Log',
                'emoji' => ':boom:',
                'level' => env('LOG_LEVEL', 'critical'),
            ],
        ],
    ],
];
