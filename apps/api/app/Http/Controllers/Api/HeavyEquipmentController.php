<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\HeavyEquipmentDetail;
use App\Services\HeavyEquipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeavyEquipmentController extends Controller
{
    public function __construct(private HeavyEquipmentService $equipmentService) {}

    /** POST /heavy-equipment/{asset}/project-booking */
    public function createProjectBooking(Request $request, Asset $asset): JsonResponse
    {
        $data = $request->validate([
            'starts_at'              => 'required|date|after_or_equal:today',
            'ends_at'                => 'required|date|after:starts_at',
            'project_description'    => 'nullable|string',
            'site_location_address'  => 'nullable|string|max:500',
            'site_latitude'          => 'nullable|numeric|between:-90,90',
            'site_longitude'         => 'nullable|numeric|between:-180,180',
            'agreed_daily_hours'     => 'nullable|integer|min:1|max:24',
            'fuel_arrangement'       => 'nullable|in:client,owner',
            'client_notes'           => 'nullable|string',
        ]);

        $booking = $this->equipmentService->createProjectBooking(
            array_merge($data, ['asset_id' => $asset->id]),
            Auth::user()
        );

        return response()->json($booking, 201);
    }

    /** POST /bookings/{booking}/owner-approve */
    public function ownerApprove(Booking $booking): JsonResponse
    {
        $this->equipmentService->ownerApproveBooking($booking, Auth::user());
        return response()->json(['message' => 'Booking approved.']);
    }

    /** POST /bookings/{booking}/owner-decline */
    public function ownerDecline(Request $request, Booking $booking): JsonResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->equipmentService->ownerDeclineBooking($booking, Auth::user(), $data['reason']);
        return response()->json(['message' => 'Booking declined.']);
    }
}
