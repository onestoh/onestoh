<?php
return [
    'rate_limits' => [
        'auth'        => env('RATE_LIMIT_AUTH', 20),
        'payment'     => env('RATE_LIMIT_PAYMENT', 30),
        'api_default' => env('RATE_LIMIT_API', 120),
    ],

    'brute_force' => [
        'max_attempts'  => env('BRUTE_FORCE_MAX_ATTEMPTS', 5),
        'decay_minutes' => env('BRUTE_FORCE_DECAY_MINUTES', 15),
    ],

    'sql_injection_patterns' => [
        '/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b/i',
        '/--/',
        '/\/\*.*?\*\//s',
        '/\bOR\b\s+\d+\s*=\s*\d+/i',
        '/\bAND\b\s+\d+\s*=\s*\d+/i',
        '/;/',
        '/\bXP_/i',
        '/\bSYSOBJECTS\b/i',
        '/\bINFORMATION_SCHEMA\b/i',
        "/\bWAITFOR\b/i",
    ],

    'file_upload' => [
        'blocked_extensions' => ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'bat', 'cmd', 'com', 'scr', 'js', 'vbs', 'jar'],
        'max_size_kb'        => env('FILE_UPLOAD_MAX_KB', 5120),
    ],

    'csp' => env('CSP_POLICY', "default-src 'self'; script-src 'self' 'nonce-{nonce}'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self'"),
];
