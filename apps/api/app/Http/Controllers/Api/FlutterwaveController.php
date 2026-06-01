<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\FlutterwaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlutterwaveController extends Controller
{
    public function __construct(private FlutterwaveService $flutterwaveService) {}

    /** POST /payments/flutterwave/initiate */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'booking_id'   => 'required|exists:bookings,id',
            'redirect_url' => 'required|url',
            'card'         => 'nullable|array',
            'card.card_number'  => 'nullable|string',
            'card.cvv'          => 'nullable|string',
            'card.expiry_month' => 'nullable|string',
            'card.expiry_year'  => 'nullable|string',
        ]);

        $booking = Booking::where('id', $data['booking_id'])
            ->where('client_id', Auth::id())
            ->firstOrFail();

        $result = $this->flutterwaveService->initiatePayment(
            $booking,
            $data['card'] ?? [],
            $data['redirect_url']
        );

        return response()->json($result);
    }

    /** GET /payments/flutterwave/verify/{txRef} */
    public function verify(string $txRef): JsonResponse
    {
        $data = $this->flutterwaveService->verifyPayment($txRef);
        return response()->json($data);
    }

    /** POST /payments/flutterwave/webhook — no auth middleware */
    public function webhook(Request $request): JsonResponse
    {
        $this->flutterwaveService->handleWebhook($request->all());
        return response()->json(['status' => 'ok']);
    }
}
