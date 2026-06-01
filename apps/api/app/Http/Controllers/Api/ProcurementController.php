<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{ProcurementTender, TenderBid};
use App\Services\GovernmentProcurementService;
use Illuminate\Http\{JsonResponse, Request};

class ProcurementController extends Controller
{
    public function __construct(private GovernmentProcurementService $service) {}

    /**
     * Return open tenders with optional filters.
     */
    public function getOpenTenders(Request $request): JsonResponse
    {
        $request->validate([
            'category'   => 'sometimes|string',
            'county'     => 'sometimes|string',
            'max_budget' => 'sometimes|numeric|min:0',
        ]);

        $tenders = $this->service->getOpenTenders($request->only('category', 'county', 'max_budget'));
        return response()->json(['data' => $tenders, 'count' => $tenders->count()]);
    }

    /**
     * Get a single tender with its bids summary.
     */
    public function getTender(ProcurementTender $tender): JsonResponse
    {
        return response()->json(['data' => $tender->load(['entity', 'bids'])]);
    }

    /**
     * Submit a bid for a tender.
     */
    public function submitBid(Request $request, ProcurementTender $tender): JsonResponse
    {
        $data = $request->validate([
            'offered_assets'         => 'required|array|min:1',
            'offered_assets.*.asset_id' => 'required|exists:assets,id',
            'offered_assets.*.rate'     => 'required|numeric|min:0',
            'offered_assets.*.availability_confirmed' => 'required|boolean',
            'rate_per_unit'          => 'required|numeric|min:0',
            'proposal_notes'         => 'sometimes|string|max:2000',
            'compliance_documents'   => 'sometimes|array',
        ]);

        $user = auth()->user();
        $yard = $user->yards()->first();
        abort_if(!$yard, 422, 'No yard found for this user.');

        $bid = $this->service->submitBid($tender, $yard, $data);
        return response()->json(['data' => $bid], 201);
    }

    /**
     * List the authenticated owner's bid history.
     */
    public function getMyBids(): JsonResponse
    {
        $bids = TenderBid::where('owner_id', auth()->id())
            ->with('tender.entity')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($bids);
    }

    /**
     * Get compliance report for tender eligibility.
     */
    public function getComplianceReport(): JsonResponse
    {
        $user = auth()->user();
        $yard = $user->yards()->first();
        abort_if(!$yard, 422, 'No yard found for this user.');

        $report = $this->service->generateComplianceReport($yard);
        return response()->json(['data' => $report]);
    }
}
