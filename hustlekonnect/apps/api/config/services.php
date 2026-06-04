<?php
return [
    'mpesa' => [
        'env'             => env('MPESA_ENV', 'sandbox'),
        'consumer_key'    => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode'       => env('MPESA_SHORTCODE'),
        'passkey'         => env('MPESA_PASSKEY'),
        'callback_url'    => env('MPESA_CALLBACK_URL'),
        'b2c_shortcode'   => env('MPESA_B2C_SHORTCODE'),
        'initiator'       => env('MPESA_B2C_INITIATOR'),
        'security_cred'   => env('MPESA_B2C_SECURITY_CREDENTIAL'),
    ],
    'flutterwave' => [
        'public_key'      => env('FLUTTERWAVE_PUBLIC_KEY'),
        'secret_key'      => env('FLUTTERWAVE_SECRET_KEY'),
        'encryption_key'  => env('FLUTTERWAVE_ENCRYPTION_KEY'),
        'webhook_hash'    => env('FLUTTERWAVE_WEBHOOK_HASH'),
    ],
    'paystack' => [
        'public_key'      => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key'      => env('PAYSTACK_SECRET_KEY'),
        'webhook_secret'  => env('PAYSTACK_WEBHOOK_SECRET'),
        'base_url'        => 'https://api.paystack.co',
    ],
    'stripe' => [
        'key'             => env('STRIPE_KEY'),
        'secret'          => env('STRIPE_SECRET'),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET'),
    ],
    'mtn_momo' => [
        'env'              => env('MTN_MOMO_ENV', 'sandbox'),
        'api_user'         => env('MTN_MOMO_API_USER'),
        'api_key'          => env('MTN_MOMO_API_KEY'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'callback_url'     => env('MTN_MOMO_CALLBACK_URL'),
    ],
    'africastalking' => [
        'username'         => env('AFRICASTALKING_USERNAME', 'sandbox'),
        'api_key'          => env('AFRICASTALKING_API_KEY'),
        'from'             => env('AFRICASTALKING_FROM', 'HustleKonnect'),
    ],
    'sendgrid' => [
        'api_key'          => env('SENDGRID_API_KEY'),
    ],
    'firebase' => [
        'server_key'       => env('FIREBASE_SERVER_KEY'),
        'sender_id'        => env('FIREBASE_SENDER_ID'),
    ],
    'smile_identity' => [
        'partner_id'       => env('SMILE_IDENTITY_PARTNER_ID'),
        'api_key'          => env('SMILE_IDENTITY_API_KEY'),
        'env'              => env('SMILE_IDENTITY_ENV', 'sandbox'),
    ],
    'anthropic' => [
        'api_key'          => env('ANTHROPIC_API_KEY'),
        'model'            => 'claude-haiku-4-5-20251001',
    ],
    'aws' => [
        'key'              => env('AWS_ACCESS_KEY_ID'),
        'secret'           => env('AWS_SECRET_ACCESS_KEY'),
        'region'           => env('AWS_DEFAULT_REGION', 'af-south-1'),
        'bucket'           => env('AWS_BUCKET'),
    ],
    'quickbooks' => [
        'client_id'        => env('QUICKBOOKS_CLIENT_ID'),
        'client_secret'    => env('QUICKBOOKS_CLIENT_SECRET'),
        'redirect_uri'     => env('QUICKBOOKS_REDIRECT_URI'),
        'base_url'         => 'https://quickbooks.api.intuit.com',
    ],
    'xero' => [
        'client_id'        => env('XERO_CLIENT_ID'),
        'client_secret'    => env('XERO_CLIENT_SECRET'),
        'redirect_uri'     => env('XERO_REDIRECT_URI'),
        'base_url'         => 'https://api.xero.com',
    ],
    'yardgroup' => [
        'shared_secret'    => env('YARDGROUP_SHARED_SECRET'),
        'estate_yard_url'  => env('ESTATE_YARD_URL', 'https://estateyard.com'),
        'motor_yard_url'   => env('MOTOR_YARD_URL', 'https://motoryard.com'),
    ],
];
