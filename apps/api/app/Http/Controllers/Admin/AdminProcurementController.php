<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{GovernmentEntity, ProcurementTender, TenderBid};
use App\Services\GovernmentProcurementService;
use Illuminate\Http\{JsonResponse, Request};

class AdminProcurementController extends Controller
{
    public function __construct(private GovernmentProcurementService $service) {}

    /**
     * List all tenders with optional status filter.
     */
    public function getTenders(Request $request): JsonResponse
    {
        $request->validate(['status' => 'sometimes|in:open,closed,awarded,cancelled']);

        $tenders = ProcurementTender::with('entity')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($tenders);
    }

    /**
     * Create a tender on behalf of a government entity.
     */
    public function createTender(Request $request): JsonResponse
    {
        $data = $request->validate([
            'government_entity_id' => 'required|exists:government_entities,id',
            'tender_number'        => 'required|string|max:60|unique:procurement_tenders',
            'title'                => 'required|string|max:255',
            'description'          => 'required|string',
            'asset_category'       => 'required|in:passenger_car,suv_4x4,van_minibus,pickup_truck,heavy_truck,excavator,tractor_farm,crane_lift,generator,special_equipment',
            'quantity_required'    => 'required|integer|min:1',
            'duration_type'        => 'required|in:daily,weekly,monthly',
            'duration_value'       => 'required|integer|min:1',
            'budget_per_unit'      => 'required|numeric|min:0',
            'total_budget'         => 'required|numeric|min:0',
            'submission_deadline'  => 'required|date|after:today',
            'service_start_date'   => 'required|date',
            'service_end_date'     => 'required|date|after:service_start_date',
            'requirements'         => 'sometimes|array',
        ]);

        $tender = ProcurementTender::create($data);
        $this->service->notifyEligibleYards($tender);

        return response()->json(['data' => $tender], 201);
    }

    /**
     * Run the bid scoring algorithm for a tender.
     */
    public function evaluateBids(ProcurementTender $tender): JsonResponse
    {
        $scored = $this->service->evaluateBids($tender);
        return response()->json(['data' => $scored]);
    }

    /**
     * Award a tender to the winning bid.
     */
    public function awardTender(Request $request, ProcurementTender $tender): JsonResponse
    {
        $request->validate(['bid_id' => 'required|exists:tender_bids,id']);
        $winningBid = TenderBid::findOrFail($request->bid_id);
        abort_if($winningBid->tender_id !== $tender->id, 422, 'Bid does not belong to this tender.');

        $this->service->awardTender($tender, $winningBid);
        return response()->json(['message' => 'Tender awarded successfully.', 'data' => $tender->fresh()]);
    }

    /**
     * List and manage verified government entities.
     */
    public function getGovernmentEntities(): JsonResponse
    {
        $entities = GovernmentEntity::orderBy('name')->paginate(20);
        return response()->json($entities);
    }
}
