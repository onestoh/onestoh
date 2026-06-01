<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\MtnMomoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MtnMomoController extends Controller
{
    public function __construct(private MtnMomoService $service) {}

    public function initiate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id'    => 'required|exists:bookings,id',
            'mobile_number' => 'required|string|min:9|max:15',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        if ($booking->renter_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        try {
            $result = $this->service->initiatePayment($booking, $validated['mobile_number']);
            return response()->json(['data' => $result]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function status(Request $request, string $requestId): JsonResponse
    {
        try {
            $status = $this->service->checkPaymentStatus($requestId);
            return response()->json(['data' => $status]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('MTN MoMo webhook received', ['payload' => $payload]);

        try {
            $this->service->handleCallback($payload);
        } catch (\Exception $e) {
            Log::error('MTN MoMo webhook handler error', ['error' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }
}
