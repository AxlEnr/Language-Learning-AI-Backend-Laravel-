<?php

return [
    'default' => env('QUEUE_CONNECTION', 'database'),
    'connections' => [
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('QUEUE_CONNECTION', 'default'),
            'retry_after' => (int) env('QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],
        'sync' => [
            'driver' => 'sync',
        ],
    ],
    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'pgsql'),
        'table' => env('QUEUE_FAILED_TABLE', 'failed_jobs'),
    ],
];
