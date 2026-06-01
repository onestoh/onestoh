<?php
return [
    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => ['token' => env('POSTMARK_TOKEN')],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'af-south-1'),
    ],

    'mpesa' => [
        'consumer_key'             => env('MPESA_CONSUMER_KEY'),
        'consumer_secret'          => env('MPESA_CONSUMER_SECRET'),
        'shortcode'                => env('MPESA_SHORTCODE'),
        'pass_key'                 => env('MPESA_PASS_KEY'),
        'b2c_shortcode'            => env('MPESA_B2C_SHORTCODE'),
        'b2c_initiator_name'       => env('MPESA_B2C_INITIATOR_NAME'),
        'b2c_security_credential'  => env('MPESA_B2C_SECURITY_CREDENTIAL'),
        'sandbox'                  => env('MPESA_SANDBOX', true),
    ],

    'africastalking' => [
        'username'  => env('AFRICASTALKING_USERNAME'),
        'api_key'   => env('AFRICASTALKING_API_KEY'),
        'sender_id' => env('AFRICASTALKING_SENDER_ID', 'TheYard'),
    ],

    'firebase' => [
        'project_id'           => env('FIREBASE_PROJECT_ID'),
        'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH'),
    ],
];
