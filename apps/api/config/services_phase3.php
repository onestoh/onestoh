<?php
// Phase 3 service configuration additions.
// Merge these entries into your existing config/services.php

return [
    'anthropic' => [
        'api_key'    => env('ANTHROPIC_API_KEY'),
        'model'      => env('ANTHROPIC_MODEL', 'claude-haiku-4-5-20251001'),
        'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 1024),
    ],

    'mtn_momo' => [
        'environment'       => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
        'subscription_key'  => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'api_user'          => env('MTN_MOMO_API_USER'),
        'api_key'           => env('MTN_MOMO_API_KEY'),
        'collections_base_url' => env('MTN_MOMO_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
        'callback_url'      => env('MTN_MOMO_CALLBACK_URL'),
    ],

    'stripe' => [
        'key'            => env('STRIPE_PUBLIC_KEY'),
        'secret'         => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'open_exchange_rates' => [
        'app_id' => env('OPEN_EXCHANGE_RATES_APP_ID'),
        'base'   => 'USD',
    ],

    'aws_rekognition' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_REKOGNITION_REGION', 'eu-west-1'),
    ],
];
