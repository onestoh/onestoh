<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Resource not found.'], 404);
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage() ?: 'HTTP error.'], $e->getStatusCode());
        });
    })->create();
