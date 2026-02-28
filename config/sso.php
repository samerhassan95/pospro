<?php

return [
    'enabled' => env('SSO_ENABLED', false),
    'secret_key' => env('SSO_SECRET_KEY', ''),
    'token_expiry' => env('SSO_TOKEN_EXPIRY', 0), // 0 = no expiration
    'allow_auto_registration' => env('SSO_ALLOW_AUTO_REGISTRATION', true),
    
    'encryption' => [
        'cipher' => 'AES-256-CBC',
        'hash_algo' => 'sha256',
    ],

    'rate_limit' => [
        'max_attempts' => 10,
        'decay_minutes' => 1,
    ],

    'log_channel' => env('SSO_LOG_CHANNEL', 'stack'),
];
