<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Asset, Geofence, TelematicsTrip};
use App\Services\TelematicsService;
use Illuminate\Http\{JsonResponse, Request};

class TelematicsController extends Controller
{
    public function __construct(private TelematicsService $service) {}

    /**
     * Receive IoT device ping (device API-key auth via telematics.device middleware).
     */
    public function ingestPing(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id'       => 'required|string|max:60',
            'latitude'        => 'required|numeric|between:-90,90',
            'longitude'       => 'required|numeric|between:-180,180',
            'speed_kmh'       => 'sometimes|integer|min:0|max:500',
            'heading'         => 'sometimes|integer|min:0|max:359',
            'odometer_km'     => 'sometimes|integer|min:0',
            'fuel_level_pct'  => 'sometimes|integer|min:0|max:100',
            'ignition_on'     => 'sometimes|boolean',
            'engine_on'       => 'sometimes|boolean',
            'battery_voltage' => 'sometimes|numeric|min:0|max:30',
            'pinged_at'       => 'sometimes|date',
        ]);

        $ping = $this->service->processPing($data);
        return response()->json(['status' => 'ok', 'ping_id' => $ping->id], 201);
    }

    /**
     * Get latest live location for an asset.
     */
    public function getLiveLocation(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $location = $this->service->getLiveLocation($asset);
        return response()->json(['data' => $location]);
    }

    /**
     * Get ordered route coordinates for a date.
     */
    public function getAssetRoute(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $request->validate(['date' => 'required|date_format:Y-m-d']);
        $route = $this->service->getAssetRoute($asset, $request->date);
        return response()->json(['data' => $route, 'count' => count($route)]);
    }

    /**
     * Paginated trip history for an asset.
     */
    public function getTripHistory(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $trips = $this->service->getTripHistory($asset, 20);
        return response()->json(['data' => $trips]);
    }

    /**
     * Driver behaviour report.
     */
    public function getDriverBehaviourReport(Request $request): JsonResponse
    {
        $request->validate(['period' => 'sometimes|string|in:7d,30d,this_month']);
        $driverId = auth()->id();
        $report   = $this->service->getDriverBehaviourReport($driverId, $request->get('period', '30d'));
        return response()->json(['data' => $report]);
    }

    /**
     * List geofences for an asset.
     */
    public function getGeofences(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        return response()->json(['data' => Geofence::where('asset_id', $asset->id)->get()]);
    }

    /**
     * Create a geofence for an asset.
     */
    public function createGeofence(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('update', $asset);
        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'type'                => 'required|in:circle,polygon',
            'center_latitude'     => 'required_if:type,circle|numeric',
            'center_longitude'    => 'required_if:type,circle|numeric',
            'radius_meters'       => 'required_if:type,circle|integer|min:50|max:100000',
            'polygon_coordinates' => 'required_if:type,polygon|array|min:3',
            'alert_on_entry'      => 'sometimes|boolean',
            'alert_on_exit'       => 'sometimes|boolean',
        ]);

        $geofence = Geofence::create(array_merge($data, ['asset_id' => $asset->id]));
        return response()->json(['data' => $geofence], 201);
    }

    /**
     * Delete a geofence.
     */
    public function deleteGeofence(Geofence $geofence): JsonResponse
    {
        $this->authorize('update', $geofence->asset);
        $geofence->delete();
        return response()->json(['message' => 'Geofence deleted.']);
    }
}
