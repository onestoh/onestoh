<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        $allowed = array_map('trim', explode(',', $roles));
        $userRole = session('role');

        if (!$userRole || !in_array($userRole, $allowed)) {
            return redirect('/dashboard')->with('error', 'Access denied: insufficient permissions.');
        }

        return $next($request);
    }
}
