<?php

return [
    'notifications' => [
        'discord' => [
            'enabled' => env('NOTIFICATIONS_DISCORD_ENABLED', true),
        ],

        'slack' => [
            'enabled' => env('NOTIFICATIONS_SLACK_ENABLED', false),
        ],
    ],
];
