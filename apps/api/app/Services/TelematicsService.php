<?php
namespace App\Services;

use App\Events\GeofenceBreachEvent;
use App\Models\{Asset, Geofence, TelematicsDevice, TelematicsPing, TelematicsTrip};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{Cache, Log, Notification};
use App\Notifications\SpeedingAlertNotification;
use App\Notifications\GeofenceBreachNotification;

class TelematicsService
{
    /**
     * Receive and persist an IoT device ping.
     */
    public function processPing(array $data): TelematicsPing
    {
        $device = TelematicsDevice::where('device_id', $data['device_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $ping = TelematicsPing::create([
            'asset_id'        => $device->asset_id,
            'device_id'       => $data['device_id'],
            'latitude'        => $data['latitude'],
            'longitude'       => $data['longitude'],
            'speed_kmh'       => $data['speed_kmh'] ?? 0,
            'heading'         => $data['heading'] ?? null,
            'odometer_km'     => $data['odometer_km'] ?? null,
            'fuel_level_pct'  => $data['fuel_level_pct'] ?? null,
            'ignition_on'     => $data['ignition_on'] ?? false,
            'engine_on'       => $data['engine_on'] ?? false,
            'battery_voltage' => $data['battery_voltage'] ?? null,
            'alerts'          => [],
            'pinged_at'       => $data['pinged_at'] ?? now(),
        ]);

        // Update device last-seen
        $device->update(['last_ping_at' => $ping->pinged_at]);

        // Cache live location in Redis (5-min TTL)
        Cache::put(
            "asset:location:{$device->asset_id}",
            [
                'latitude'   => $ping->latitude,
                'longitude'  => $ping->longitude,
                'speed_kmh'  => $ping->speed_kmh,
                'heading'    => $ping->heading,
                'pinged_at'  => $ping->pinged_at->toISOString(),
                'ignition_on' => $ping->ignition_on,
            ],
            300
        );

        // Speeding alert (>120 km/h)
        if ($ping->speed_kmh > 120) {
            $this->dispatchSpeedingAlert($ping);
        }

        // Geofence checks
        $this->checkGeofences($ping);

        return $ping;
    }

    /**
     * Check all active geofences for the asset and fire breach events.
     */
    public function checkGeofences(TelematicsPing $ping): void
    {
        $geofences = Geofence::where('asset_id', $ping->asset_id)
            ->where('is_active', true)
            ->get();

        foreach ($geofences as $geofence) {
            $inside = $geofence->containsPoint($ping->latitude, $ping->longitude);
            $cacheKey = "geofence:state:{$geofence->id}:{$ping->asset_id}";
            $previouslyInside = Cache::get($cacheKey, false);

            if ($inside !== $previouslyInside) {
                Cache::put($cacheKey, $inside, 3600);
                $direction = $inside ? 'entry' : 'exit';

                if (($inside && $geofence->alert_on_entry) || (!$inside && $geofence->alert_on_exit)) {
                    event(new GeofenceBreachEvent($ping->asset, $geofence, $direction, $ping));
                }
            }
        }
    }

    /**
     * Start a new trip record when ignition turns on.
     */
    public function startTrip(string $deviceId, float $lat, float $lng): TelematicsTrip
    {
        $device = TelematicsDevice::where('device_id', $deviceId)->firstOrFail();

        return TelematicsTrip::create([
            'asset_id'        => $device->asset_id,
            'start_latitude'  => $lat,
            'start_longitude' => $lng,
            'started_at'      => now(),
        ]);
    }

    /**
     * Close a trip and compute summary stats + driver score.
     */
    public function endTrip(TelematicsTrip $trip, array $finalPing): TelematicsTrip
    {
        $distanceKm = $this->calculateTripDistance($trip);

        $score = 100;
        $score -= min(30, $trip->speeding_events * 5);
        $score -= min(20, $trip->harsh_braking_events * 4);
        if ($trip->idle_seconds > 600) {
            $score -= min(10, (int)(($trip->idle_seconds - 600) / 120));
        }
        $score = max(0, $score);

        $trip->update([
            'end_latitude'   => $finalPing['latitude'],
            'end_longitude'  => $finalPing['longitude'],
            'ended_at'       => now(),
            'distance_km'    => $distanceKm,
            'driver_score'   => $score,
        ]);

        return $trip->fresh();
    }

    /**
     * Return the latest asset location from Redis cache or fall back to DB.
     */
    public function getLiveLocation(Asset $asset): ?array
    {
        $cached = Cache::get("asset:location:{$asset->id}");
        if ($cached) {
            return $cached;
        }

        $latest = TelematicsPing::where('asset_id', $asset->id)
            ->orderByDesc('pinged_at')
            ->first();

        if (!$latest) {
            return null;
        }

        return [
            'latitude'    => $latest->latitude,
            'longitude'   => $latest->longitude,
            'speed_kmh'   => $latest->speed_kmh,
            'heading'     => $latest->heading,
            'pinged_at'   => $latest->pinged_at->toISOString(),
            'ignition_on' => $latest->ignition_on,
        ];
    }

    /**
     * Return ordered array of coordinates for an asset on a given date.
     */
    public function getAssetRoute(Asset $asset, string $date): array
    {
        return TelematicsPing::where('asset_id', $asset->id)
            ->whereDate('pinged_at', $date)
            ->orderBy('pinged_at')
            ->get(['latitude', 'longitude', 'speed_kmh', 'pinged_at'])
            ->map(fn ($p) => [
                'lat' => $p->latitude,
                'lng' => $p->longitude,
                'speed_kmh' => $p->speed_kmh,
                'time' => $p->pinged_at->toISOString(),
            ])
            ->toArray();
    }

    /**
     * Return driver behaviour report for a given period (e.g. '7d', '30d', 'this_month').
     */
    public function getDriverBehaviourReport(int $driverId, string $period): array
    {
        $from = match ($period) {
            '7d'         => now()->subDays(7),
            '30d'        => now()->subDays(30),
            'this_month' => now()->startOfMonth(),
            default      => now()->subDays(30),
        };

        // Trips linked to bookings driven by this user
        $trips = TelematicsTrip::whereHas(
            'booking',
            fn ($q) => $q->where('renter_id', $driverId)
        )->where('started_at', '>=', $from)->get();

        $totalTrips    = $trips->count();
        $totalDistance = $trips->sum('distance_km');
        $avgScore      = $totalTrips ? round($trips->avg('driver_score'), 1) : null;
        $speedingEvents = $trips->sum('speeding_events');
        $harshBraking   = $trips->sum('harsh_braking_events');

        return [
            'driver_id'       => $driverId,
            'period'          => $period,
            'total_trips'     => $totalTrips,
            'total_distance_km' => $totalDistance,
            'avg_driver_score'  => $avgScore,
            'speeding_events'   => $speedingEvents,
            'harsh_braking_events' => $harshBraking,
        ];
    }

    /**
     * Return paginated trip history for an asset.
     */
    public function getTripHistory(Asset $asset, int $limit = 20): Collection
    {
        return TelematicsTrip::where('asset_id', $asset->id)
            ->orderByDesc('started_at')
            ->limit($limit)
            ->get();
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function dispatchSpeedingAlert(TelematicsPing $ping): void
    {
        try {
            $asset = $ping->asset()->with('owner')->first();
            if ($asset && $asset->owner) {
                $asset->owner->notify(new SpeedingAlertNotification($ping));
            }
        } catch (\Throwable $e) {
            Log::warning('Speeding alert dispatch failed', ['error' => $e->getMessage()]);
        }
    }

    private function calculateTripDistance(TelematicsTrip $trip): int
    {
        $pings = TelematicsPing::where('asset_id', $trip->asset_id)
            ->whereBetween('pinged_at', [$trip->started_at, now()])
            ->orderBy('pinged_at')
            ->get(['latitude', 'longitude']);

        $total = 0;
        for ($i = 1; $i < $pings->count(); $i++) {
            $prev = $pings[$i - 1];
            $curr = $pings[$i];
            $total += $this->haversine($prev->latitude, $prev->longitude, $curr->latitude, $curr->longitude);
        }
        return (int) round($total / 1000);
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000;
        $phi1 = deg2rad($lat1); $phi2 = deg2rad($lat2);
        $dphi = deg2rad($lat2 - $lat1); $dl = deg2rad($lng2 - $lng1);
        $a = sin($dphi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dl / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
