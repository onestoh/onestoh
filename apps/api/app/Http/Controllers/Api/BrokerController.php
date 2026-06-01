<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralClick;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrokerController extends Controller
{
    public function __construct(private ReferralService $referralService) {}

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'broker') {
            return response()->json(['message' => 'Only brokers can access this endpoint.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'from' => 'sometimes|date',
            'to'   => 'sometimes|date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $stats = $this->referralService->getBrokerStats($user, $from, $to);

        return response()->json(array_merge($stats, [
            'referral_code' => $user->referral_code,
            'period'        => ['from' => $from, 'to' => $to],
        ]));
    }

    public function commissionHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'broker') {
            return response()->json(['message' => 'Only brokers can access this endpoint.'], 403);
        }

        return response()->json($this->referralService->getCommissionHistory($user));
    }

    public function trackClick(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string|size:12',
            'asset_id'      => 'sometimes|integer|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->referralService->recordClick($request->referral_code, $request->asset_id, $request);

        return response()->json(['message' => 'Click tracked.']);
    }

    public function generateLink(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'broker') {
            return response()->json(['message' => 'Only brokers can access this endpoint.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'asset_id' => 'sometimes|integer|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $baseUrl  = config('app.frontend_url', 'https://theonlineyard.co.ke');
        $assetPath = $request->filled('asset_id') ? "/assets/{$request->asset_id}" : '';
        $link = "{$baseUrl}{$assetPath}?ref={$user->referral_code}";

        return response()->json([
            'referral_link' => $link,
            'referral_code' => $user->referral_code,
        ]);
    }
}
