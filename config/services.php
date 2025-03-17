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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'payment' => [
        'secret_key' => env('PAYMENT_SERVICE_SECRET_KEY'),
        'login' => env('PAYMENT_API_LOGIN'),
        'password' => env('PAYMENT_API_PASSWORD'),
        'allowed_callback_ips' => array_filter(
            explode(',', env('PAYMENT_SERVICE_CALLBACK_IP_WHITELIST'))
        ),
    ],

    'marketplace' => [
        'credentials' => [
            'client_id' => env('MARKETPLACE_CLIENT_ID'),
            'secret_key' => env('MARKETPLACE_SECRET_KEY'),
        ],
        'currency_code' => env('MARKETPLACE_CURRENCY'),
    ]
];
