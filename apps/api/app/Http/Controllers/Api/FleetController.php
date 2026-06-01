<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Services\FleetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FleetController extends Controller
{
    public function __construct(private FleetService $fleetService) {}

    /** GET /fleet/summary */
    public function summary(): JsonResponse
    {
        return response()->json($this->fleetService->getFleetSummary(Auth::user()));
    }

    /** GET /fleet/calendar?from=YYYY-MM-DD&to=YYYY-MM-DD */
    public function calendar(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $calendar = $this->fleetService->getFleetCalendar(
            Auth::user(),
            $request->from,
            $request->to
        );

        return response()->json($calendar);
    }

    /** GET /fleet/analytics?asset_id=&month=YYYY-MM */
    public function analytics(Request $request): JsonResponse
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'month'    => 'required|date_format:Y-m',
        ]);

        $asset = Asset::where('id', $request->asset_id)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $pnl = $this->fleetService->getAssetPnL($asset, $request->month);
        $this->fleetService->refreshAnalyticsCache($asset);

        return response()->json(array_merge($pnl, [
            'cache' => $asset->analyticsCache,
        ]));
    }

    /** POST /fleet/maintenance */
    public function logMaintenance(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id'            => 'required|exists:assets,id',
            'service_type'        => 'required|string',
            'service_date'        => 'required|date',
            'cost'                => 'nullable|numeric|min:0',
            'description'         => 'nullable|string',
            'service_centre'      => 'nullable|string|max:255',
            'odometer_at_service' => 'nullable|integer',
            'next_service_km'     => 'nullable|integer',
            'next_service_date'   => 'nullable|date',
            'attachments'         => 'nullable|array',
        ]);

        $asset = Asset::where('id', $data['asset_id'])
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $record = $this->fleetService->logMaintenance($asset, $data, Auth::user());

        return response()->json($record, 201);
    }

    /** GET /fleet/maintenance?asset_id= */
    public function maintenanceHistory(Request $request): JsonResponse
    {
        $request->validate(['asset_id' => 'required|exists:assets,id']);

        $asset = Asset::where('id', $request->asset_id)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $records = MaintenanceRecord::where('asset_id', $asset->id)
            ->orderByDesc('service_date')
            ->paginate(20);

        return response()->json($records);
    }

    /** POST /fleet/maintenance/schedule */
    public function createSchedule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id'          => 'required|exists:assets,id',
            'name'              => 'required|string|max:255',
            'interval_type'     => 'required|in:km,days,months',
            'interval_value'    => 'required|integer|min:1',
            'last_done_at'      => 'nullable|date',
            'next_due_at'       => 'nullable|date',
            'alert_days_before' => 'nullable|integer|min:1',
            'is_active'         => 'nullable|boolean',
        ]);

        Asset::where('id', $data['asset_id'])->where('owner_id', Auth::id())->firstOrFail();

        $schedule = MaintenanceSchedule::create($data);

        return response()->json($schedule, 201);
    }

    /** GET /fleet/idle-assets */
    public function idleAssets(): JsonResponse
    {
        $idle = $this->fleetService->getIdleAssets(Auth::user());
        return response()->json($idle);
    }
}
