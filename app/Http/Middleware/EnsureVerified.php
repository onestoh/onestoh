<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->status !== 'verified') {
            return redirect()->route('verification.pending')
                ->with('warning', 'Your account must be verified to access this area.');
        }

        return $next($request);
    }
}
