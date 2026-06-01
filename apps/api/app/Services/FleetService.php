<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAnalyticsCache;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class FleetService
{
    /**
     * Return high-level fleet statistics for an owner.
     */
    public function getFleetSummary(User $owner): array
    {
        $assets = Asset::where('owner_id', $owner->id)->get();
        $assetIds = $assets->pluck('id');

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        $revenueThisMonth = Booking::whereIn('asset_id', $assetIds)
            ->where('status', 'completed')
            ->whereBetween('starts_at', [$startOfMonth, $now])
            ->sum('total_price');

        $underMaintenance = MaintenanceRecord::whereIn('asset_id', $assetIds)
            ->whereDate('service_date', $now->toDateString())
            ->distinct('asset_id')
            ->count('asset_id');

        $utilisationRates = $assets->map(fn ($a) => $this->getUtilisationRate($a, 30));
        $avgUtilisation = $utilisationRates->avg() ?? 0;

        return [
            'total_assets'       => $assets->count(),
            'total_active'       => $assets->where('status', 'available')->count(),
            'under_maintenance'  => $underMaintenance,
            'revenue_mtd'        => round((float) $revenueThisMonth, 2),
            'avg_utilisation_pct'=> round($avgUtilisation, 2),
        ];
    }

    /**
     * Log a maintenance event and block availability slots on that date.
     */
    public function logMaintenance(Asset $asset, array $data, User $loggedBy): MaintenanceRecord
    {
        $record = MaintenanceRecord::create(array_merge($data, [
            'asset_id'  => $asset->id,
            'logged_by' => $loggedBy->id,
        ]));

        // Block availability slot for service date
        $serviceDate = Carbon::parse($data['service_date']);
        AvailabilitySlot::updateOrCreate(
            ['asset_id' => $asset->id, 'date' => $serviceDate->toDateString()],
            ['status' => 'blocked', 'reason' => 'maintenance']
        );

        return $record;
    }

    /**
     * Check all active maintenance schedules for the asset and notify owner if due.
     */
    public function scheduleMaintenanceAlert(Asset $asset): void
    {
        $schedules = MaintenanceSchedule::where('asset_id', $asset->id)
            ->where('is_active', true)
            ->get();

        foreach ($schedules as $schedule) {
            if ($schedule->isDueSoon()) {
                $asset->owner->notify(new \App\Notifications\MaintenanceDueNotification($asset, $schedule));
            }
        }
    }

    /**
     * Calculate utilisation rate (booked days / available days) for the given window.
     */
    public function getUtilisationRate(Asset $asset, int $days = 30): float
    {
        $from = Carbon::now()->subDays($days);
        $to   = Carbon::now();

        $bookedDays = Booking::where('asset_id', $asset->id)
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->where('starts_at', '>=', $from)
            ->where('ends_at', '<=', $to)
            ->get()
            ->sum(fn ($b) => Carbon::parse($b->starts_at)->diffInDays(Carbon::parse($b->ends_at)) ?: 1);

        return $days > 0 ? round(min(($bookedDays / $days) * 100, 100), 2) : 0.0;
    }

    /**
     * Return all assets that have had no confirmed bookings in the last 30 days.
     */
    public function getIdleAssets(User $owner): Collection
    {
        $cutoff   = Carbon::now()->subDays(30);
        $assetIds = Asset::where('owner_id', $owner->id)->pluck('id');

        $activeAssetIds = Booking::whereIn('asset_id', $assetIds)
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->where('starts_at', '>=', $cutoff)
            ->pluck('asset_id')
            ->unique();

        return Asset::where('owner_id', $owner->id)
            ->whereNotIn('id', $activeAssetIds)
            ->get();
    }

    /**
     * Return all fleet bookings within a date range, keyed by asset.
     */
    public function getFleetCalendar(User $owner, string $from, string $to): array
    {
        $assetIds = Asset::where('owner_id', $owner->id)->pluck('id');

        $bookings = Booking::with(['asset', 'client'])
            ->whereIn('asset_id', $assetIds)
            ->where('starts_at', '<=', $to)
            ->where('ends_at', '>=', $from)
            ->get();

        return $bookings->groupBy('asset_id')->map(fn ($group) => $group->map(fn ($b) => [
            'id'          => $b->id,
            'starts_at'   => $b->starts_at,
            'ends_at'     => $b->ends_at,
            'status'      => $b->status,
            'client_name' => optional($b->client)->name,
            'total_price' => $b->total_price,
        ]))->toArray();
    }

    /**
     * Return revenue minus maintenance costs for a given asset and month (format: Y-m).
     */
    public function getAssetPnL(Asset $asset, string $month): array
    {
        [$year, $mon] = explode('-', $month);
        $from = Carbon::createFromDate($year, $mon, 1)->startOfMonth();
        $to   = $from->copy()->endOfMonth();

        $revenue = Booking::where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->whereBetween('ends_at', [$from, $to])
            ->sum('total_price');

        $maintenanceCost = MaintenanceRecord::where('asset_id', $asset->id)
            ->whereBetween('service_date', [$from->toDateString(), $to->toDateString()])
            ->sum('cost');

        return [
            'month'            => $month,
            'revenue'          => round((float) $revenue, 2),
            'maintenance_cost' => round((float) $maintenanceCost, 2),
            'net_margin'       => round((float) $revenue - (float) $maintenanceCost, 2),
        ];
    }

    /**
     * Recalculate and persist the AssetAnalyticsCache record for an asset.
     */
    public function refreshAnalyticsCache(Asset $asset): void
    {
        $now           = Carbon::now();
        $startOfMonth  = $now->copy()->startOfMonth();
        $startOfLastMo = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMo   = $now->copy()->subMonthNoOverflow()->endOfMonth();
        $startOfYear   = $now->copy()->startOfYear();

        $revenueThisMonth = Booking::where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->whereBetween('ends_at', [$startOfMonth, $now])
            ->sum('total_price');

        $revenueLastMonth = Booking::where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->whereBetween('ends_at', [$startOfLastMo, $endOfLastMo])
            ->sum('total_price');

        $revenueThisYear = Booking::where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->whereBetween('ends_at', [$startOfYear, $now])
            ->sum('total_price');

        $bookingsThisMonth = Booking::where('asset_id', $asset->id)
            ->whereBetween('starts_at', [$startOfMonth, $now])
            ->count();

        $bookingsLastMonth = Booking::where('asset_id', $asset->id)
            ->whereBetween('starts_at', [$startOfLastMo, $endOfLastMo])
            ->count();

        $maintenanceCostYtd = MaintenanceRecord::where('asset_id', $asset->id)
            ->whereBetween('service_date', [$startOfYear->toDateString(), $now->toDateString()])
            ->sum('cost');

        $utilisation30d = $this->getUtilisationRate($asset, 30);
        $utilisation90d = $this->getUtilisationRate($asset, 90);

        $avgDuration = Booking::where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->selectRaw('AVG(DATEDIFF(ends_at, starts_at)) as avg_days')
            ->value('avg_days') ?? 0;

        AssetAnalyticsCache::updateOrCreate(
            ['asset_id' => $asset->id],
            [
                'revenue_this_month'       => $revenueThisMonth,
                'revenue_last_month'       => $revenueLastMonth,
                'revenue_this_year'        => $revenueThisYear,
                'utilisation_rate_30d'     => $utilisation30d,
                'utilisation_rate_90d'     => $utilisation90d,
                'bookings_this_month'      => $bookingsThisMonth,
                'bookings_last_month'      => $bookingsLastMonth,
                'avg_booking_duration_days'=> round((float) $avgDuration, 2),
                'idle_days_last_30'        => max(0, 30 - (int) round($utilisation30d * 30 / 100)),
                'maintenance_cost_ytd'     => $maintenanceCostYtd,
                'net_margin_this_month'    => (float) $revenueThisMonth - (float) $maintenanceCostYtd,
                'last_calculated_at'       => $now,
            ]
        );
    }
}
