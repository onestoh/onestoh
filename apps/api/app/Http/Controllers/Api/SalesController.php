<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\SaleOffer;
use App\Models\TestDriveBooking;
use App\Services\SalesService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    /** POST /sales/enquiry */
    public function createEnquiry(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id'    => 'required|exists:assets,id',
            'offer_price' => 'required|numeric|min:1',
        ]);

        $asset = Asset::findOrFail($data['asset_id']);
        $offer = $this->salesService->createEnquiry($asset, Auth::user(), (float) $data['offer_price']);

        return response()->json($offer, 201);
    }

    /** POST /sales/offers/{offer}/counter */
    public function counterOffer(Request $request, SaleOffer $offer): JsonResponse
    {
        $data = $request->validate(['price' => 'required|numeric|min:1']);
        abort_if(!in_array(Auth::id(), [$offer->buyer_id, $offer->seller_id]), 403);

        $offer = $this->salesService->counterOffer($offer, (float) $data['price'], Auth::user());
        return response()->json($offer);
    }

    /** POST /sales/offers/{offer}/agree */
    public function agreePrice(Request $request, SaleOffer $offer): JsonResponse
    {
        $data = $request->validate(['agreed_price' => 'required|numeric|min:1']);
        abort_if(!in_array(Auth::id(), [$offer->buyer_id, $offer->seller_id]), 403);

        $offer = $this->salesService->agreePrice($offer, (float) $data['agreed_price']);
        return response()->json($offer);
    }

    /** POST /sales/offers/{offer}/reserve */
    public function payReservation(Request $request, SaleOffer $offer): JsonResponse
    {
        $data = $request->validate(['payment_method' => 'required|string']);
        abort_if(Auth::id() !== $offer->buyer_id, 403);

        $offer = $this->salesService->payReservationFee($offer, $data['payment_method']);
        return response()->json($offer);
    }

    /** GET /sales/offers */
    public function myOffers(Request $request): JsonResponse
    {
        $userId  = Auth::id();
        $offers  = SaleOffer::with(['asset', 'buyer', 'seller'])
            ->where(fn ($q) => $q->where('buyer_id', $userId)->orWhere('seller_id', $userId))
            ->orderByDesc('updated_at')
            ->paginate(15);

        return response()->json($offers);
    }

    /** POST /sales/test-drive */
    public function scheduleTestDrive(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id'     => 'required|exists:assets,id',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $asset     = Asset::findOrFail($data['asset_id']);
        $testDrive = $this->salesService->scheduleTestDrive(
            $asset,
            Auth::user(),
            Carbon::parse($data['scheduled_at'])
        );

        return response()->json($testDrive, 201);
    }

    /** GET /sales/test-drives */
    public function myTestDrives(): JsonResponse
    {
        $testDrives = TestDriveBooking::where('client_id', Auth::id())
            ->with('asset')
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return response()->json($testDrives);
    }
}
