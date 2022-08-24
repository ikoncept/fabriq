<?php

return [
    'ikoncept_pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
            'encrypted' => true,
            'host' => 'ws.ikoncept.io',
            'port' => 443,
            'scheme' => 'https',
        ],
    ],
];
