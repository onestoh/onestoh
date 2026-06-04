<?php
return [
    'paths'                    => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000'))),
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'X-API-Key'],
    'exposed_headers'          => ['X-RateLimit-Limit', 'X-RateLimit-Remaining'],
    'max_age'                  => 3600,
    'supports_credentials'     => true,
];
