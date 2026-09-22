<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transmissor Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized alerting and messaging configuration for RicaSoluções services.
    | Handles notification broadcasts when services fail or recover.
    |
    */

    'channels' => ['slack', 'log'],

    'slack' => [
        'webhook_url' => env('TRANSMISSOR_SLACK_WEBHOOK_URL', env('LOG_SLACK_WEBHOOK_URL')),
        'channel' => env('TRANSMISSOR_SLACK_CHANNEL', '#qualidade'),
        'username' => 'Transmissor Alertas',
    ],

    'discord' => [
        'webhook_url' => env('TRANSMISSOR_DISCORD_WEBHOOK_URL'),
    ],

    'email' => [
        'to' => env('TRANSMISSOR_ALERTS_EMAIL', env('CENTRAL_ALERTS_EMAIL', 'ricardo@sierratecnologia.com.br')),
    ],
];
