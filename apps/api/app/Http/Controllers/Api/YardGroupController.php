<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\YardGroupSsoService;
use Illuminate\Http\{JsonResponse, Request};

class YardGroupController extends Controller
{
    public function __construct(private YardGroupSsoService $ssoService) {}

    /**
     * Generate a cross-platform SSO token.
     */
    public function generateSsoToken(Request $request): JsonResponse
    {
        $request->validate(['target_platform' => 'required|in:theonlineyard,estateyard,motoryard']);
        $token = $this->ssoService->generateSsoToken(auth()->user(), $request->target_platform);
        return response()->json(['sso_token' => $token, 'expires_in' => 300]);
    }

    /**
     * Validate an incoming SSO token (called by sibling platforms).
     * Protected by yardgroup.shared_secret middleware.
     */
    public function validateSsoToken(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'platform' => 'required|in:theonlineyard,estateyard,motoryard',
        ]);

        $user = $this->ssoService->validateSsoToken($request->token, $request->platform);
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired SSO token.'], 401);
        }

        return response()->json([
            'valid'            => true,
            'user_id'          => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'kyc_verified'     => $user->kyc_verified ?? false,
            'wallet_balance'   => $user->wallet_balance ?? 0,
            'bookings_count'   => $user->bookingsAsRenter()->count(),
        ]);
    }

    /**
     * Return broker cross-platform referral statistics.
     */
    public function getCrossReferralStats(): JsonResponse
    {
        $stats = $this->ssoService->getCrossReferralStats(auth()->user());
        return response()->json(['data' => $stats]);
    }

    /**
     * Return unified YardGroup profile.
     */
    public function getUnifiedProfile(): JsonResponse
    {
        $profile = $this->ssoService->getUnifiedProfile(auth()->user());
        return response()->json(['data' => $profile]);
    }
}
