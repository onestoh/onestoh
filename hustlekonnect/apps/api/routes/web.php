<?php
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json([
    'app'     => 'HustleKonnect API',
    'version' => '1.0.0',
    'status'  => 'operational',
    'docs'    => url('/api/v1'),
]));

Route::get('/health', fn() => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));
