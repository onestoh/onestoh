<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InsurancePolicy;
use App\Services\InsuranceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsuranceController extends Controller
{
    public function __construct(private InsuranceService $insuranceService) {}

    /** GET /bookings/{booking}/insurance-products */
    public function products(Booking $booking, Request $request): JsonResponse
    {
        $this->authorize('view', $booking);
        $durationType = $request->get('duration_type', 'daily');
        $products = $this->insuranceService->getAvailableProducts($booking->asset, $durationType);
        return response()->json($products);
    }

    /** POST /bookings/{booking}/insurance */
    public function addInsurance(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);
        $data = $request->validate(['product_type' => 'required|in:collision_damage_waiver,third_party_liability,comprehensive']);

        $policy = $this->insuranceService->issuePolicy($booking, $data['product_type']);
        return response()->json($policy, 201);
    }

    /** POST /insurance/{policy}/claim */
    public function fileClaim(Request $request, InsurancePolicy $policy): JsonResponse
    {
        $data = $request->validate([
            'description'    => 'required|string',
            'claimed_amount' => 'required|numeric|min:1',
            'evidence'       => 'nullable|array',
        ]);

        $claim = $this->insuranceService->fileClaim($policy, Auth::user(), $data);
        return response()->json($claim, 201);
    }

    /** GET /insurance/{policy}/certificate */
    public function certificate(InsurancePolicy $policy): JsonResponse
    {
        abort_if(!$policy->certificate_path, 404, 'No certificate available.');
        return response()->json(['certificate_path' => $policy->certificate_path]);
    }
}
