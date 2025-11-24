<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'api' => [
        // Base URL for the external Node.js API (or other backend). Set in .env as API_URL
        'url' => env('API_URL', 'http://localhost:3002'),
        // Bearer token for API authentication. Set in .env as API_TOKEN
        'token' => env('API_TOKEN', ''),
        // Timeout (seconds) for HTTP client requests
        'timeout' => env('API_TIMEOUT', 30),
        // Verify SSL certificates (true/false). Useful for local dev with self-signed certs.
        'verify' => env('API_VERIFY', true),
    ],

];
