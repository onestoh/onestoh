<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureKycVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated.'], 401);
        if ($user->kyc_status !== 'approved') {
            return response()->json(['message' => 'KYC verification required.', 'kyc_status' => $user->kyc_status, 'redirect' => '/kyc'], 403);
        }
        return $next($request);
    }
}
