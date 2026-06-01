<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Brute Force Protection
    |--------------------------------------------------------------------------
    */
    'brute_force' => [
        'max_attempts'      => (int) env('LOGIN_MAX_ATTEMPTS', 5),
        'lockout_minutes'   => (int) env('LOGIN_LOCKOUT_MINUTES', 15),
        'progressive'       => [
            5  => 15,   // 5 attempts  -> 15 min lockout
            10 => 60,   // 10 attempts -> 1 hour lockout
            20 => 1440, // 20 attempts -> 24 hour lockout
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (requests per minute)
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'auth'          => (int) env('RATE_LIMIT_AUTH', 20),
        'payment'       => (int) env('RATE_LIMIT_PAYMENT', 30),
        'api_default'   => (int) env('RATE_LIMIT_DEFAULT', 120),
        'public_api'    => (int) env('RATE_LIMIT_PUBLIC_API', 60),
        'webhook'       => (int) env('RATE_LIMIT_WEBHOOK', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins
    |--------------------------------------------------------------------------
    */
    'cors_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000'))),

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    */
    'csp_enabled' => (bool) env('CSP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_size_mb'       => (int) env('UPLOAD_MAX_SIZE_MB', 10),
        'allowed_mimes'     => ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
        'disallowed_mimes'  => ['php', 'phtml', 'exe', 'sh', 'bat', 'cmd', 'com', 'js', 'html', 'htm'],
        'scan_malware'      => (bool) env('UPLOAD_SCAN_MALWARE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session' => [
        'secure_cookie'     => (bool) env('SESSION_SECURE_COOKIE', true),
        'same_site'         => env('SESSION_SAME_SITE', 'lax'),
        'http_only'         => true,
        'lifetime_minutes'  => (int) env('SESSION_LIFETIME', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Token Security
    |--------------------------------------------------------------------------
    */
    'tokens' => [
        'sanctum_expiry_minutes'    => (int) env('SANCTUM_EXPIRY_MINUTES', 60 * 24 * 7), // 7 days
        'password_reset_expiry_min' => (int) env('PASSWORD_RESET_EXPIRY', 60),
        'otp_expiry_minutes'        => (int) env('OTP_EXPIRY_MINUTES', 10),
        'otp_length'                => (int) env('OTP_LENGTH', 6),
    ],

    /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Thresholds
    |--------------------------------------------------------------------------
    */
    'fraud_thresholds' => [
        'max_bookings_per_hour'     => (int) env('MAX_BOOKINGS_PER_HOUR', 5),
        'max_payments_per_hour'     => (int) env('MAX_PAYMENTS_PER_HOUR', 10),
        'max_failed_payments'       => (int) env('MAX_FAILED_PAYMENTS', 3),
        'high_value_threshold_kes'  => (int) env('HIGH_VALUE_THRESHOLD_KES', 500000),
    ],
];
