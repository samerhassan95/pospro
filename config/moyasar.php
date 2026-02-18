<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Moyasar Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for Moyasar payment gateway
    | integration. You can configure the API endpoints, supported payment
    | methods, and other settings here.
    |
    */

    'api_url' => env('MOYASAR_API_URL', 'https://api.moyasar.com/v1'),
    
    'supported_currencies' => [
        'SAR', // Saudi Riyal (primary)
        'USD', // US Dollar
        'EUR', // Euro
        'AED', // UAE Dirham
        'KWD', // Kuwaiti Dinar
        'BHD', // Bahraini Dinar
        'OMR', // Omani Rial
        'QAR', // Qatari Riyal
    ],

    'default_currency' => env('MOYASAR_DEFAULT_CURRENCY', 'SAR'),

    'supported_methods' => [
        'creditcard',
        'mada',
        'applepay',
        'stcpay',
    ],

    'webhook_secret' => env('MOYASAR_WEBHOOK_SECRET'),

    'timeout' => env('MOYASAR_TIMEOUT', 30), // seconds

    'retry_attempts' => env('MOYASAR_RETRY_ATTEMPTS', 3),

    'test_mode' => env('MOYASAR_TEST_MODE', false),

    // Callback URLs
    'callback_urls' => [
        'success' => env('MOYASAR_SUCCESS_URL', '/payment/success'),
        'failed' => env('MOYASAR_FAILED_URL', '/payment/failed'),
    ],

    // Payment limits
    'limits' => [
        'min_amount' => 1.00, // SAR
        'max_amount' => 100000.00, // SAR
    ],

    // UI Configuration
    'ui' => [
        'theme' => env('MOYASAR_THEME', 'default'),
        'language' => env('MOYASAR_LANGUAGE', 'ar'),
        'show_receipt' => env('MOYASAR_SHOW_RECEIPT', true),
    ],

    // Logging
    'logging' => [
        'enabled' => env('MOYASAR_LOGGING_ENABLED', true),
        'level' => env('MOYASAR_LOGGING_LEVEL', 'info'),
        'channel' => env('MOYASAR_LOGGING_CHANNEL', 'single'),
    ],

    // Security
    'security' => [
        'verify_ssl' => env('MOYASAR_VERIFY_SSL', true),
        'encrypt_keys' => env('MOYASAR_ENCRYPT_KEYS', true),
    ],
];