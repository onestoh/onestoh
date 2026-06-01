<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverAvailability;
use App\Models\User;
use App\Models\Yard;
use App\Models\YardDriverAssignment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DriverPoolService
{
    /**
     * Return drivers assigned to a yard who are available on a given date
     * and hold the required licence class.
     */
    public function getAvailableDrivers(Yard $yard, Carbon $date, string $licenceClass): Collection
    {
        $assignedDriverIds = YardDriverAssignment::where('yard_id', $yard->id)
            ->whereNull('removed_at')
            ->pluck('driver_id');

        $unavailableIds = DriverAvailability::where('date', $date->toDateString())
            ->whereIn('status', ['unavailable', 'on_leave', 'assigned'])
            ->whereIn('driver_id', $assignedDriverIds)
            ->pluck('driver_id');

        return Driver::whereIn('id', $assignedDriverIds)
            ->whereNotIn('id', $unavailableIds)
            ->where('licence_class', '>=', $licenceClass)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Auto-assign the least-recently-assigned available driver to a booking (round-robin).
     */
    public function autoAssignDriver(Booking $booking): ?Driver
    {
        $yard = $booking->asset->yard;
        if (!$yard) return null;

        $date      = Carbon::parse($booking->starts_at);
        $licClass  = optional($booking->asset)->required_licence_class ?? 'B';

        $available = $this->getAvailableDrivers($yard, $date, $licClass);
        if ($available->isEmpty()) return null;

        // Round-robin: pick driver with fewest recent assignments
        $driver = $available->sortBy(fn ($d) =>
            Booking::where('driver_id', $d->id)
                ->where('starts_at', '>=', now()->subDays(30))
                ->count()
        )->first();

        $booking->update(['driver_id' => $driver->id]);

        $this->setDriverAvailability($driver, $date->toDateString(), 'assigned');

        $driver->user->notify(new \App\Notifications\DriverAssignedNotification($booking));

        return $driver;
    }

    /**
     * Set a driver's availability status for a given date.
     */
    public function setDriverAvailability(Driver $driver, string $date, string $status): void
    {
        DriverAvailability::updateOrCreate(
            ['driver_id' => $driver->id, 'date' => $date],
            ['status'    => $status]
        );
    }

    /**
     * Compute driver performance statistics.
     */
    public function getDriverPerformance(Driver $driver): array
    {
        $bookings = Booking::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->get();

        $totalTrips    = $bookings->count();
        $avgRating     = $bookings->avg('driver_rating') ?? 0;
        $onTimeCount   = $bookings->where('driver_on_time', true)->count();
        $incidentFree  = $bookings->where('had_incident', false)->count();

        return [
            'total_trips'          => $totalTrips,
            'avg_rating'           => round((float) $avgRating, 2),
            'on_time_pct'          => $totalTrips > 0 ? round($onTimeCount / $totalTrips * 100, 2) : 0,
            'incident_free_pct'    => $totalTrips > 0 ? round($incidentFree / $totalTrips * 100, 2) : 0,
        ];
    }

    /**
     * Assign a driver to a yard.
     */
    public function assignDriverToYard(Driver $driver, Yard $yard, User $assignedBy): void
    {
        YardDriverAssignment::updateOrCreate(
            ['driver_id' => $driver->id, 'yard_id' => $yard->id],
            [
                'assigned_by' => $assignedBy->id,
                'assigned_at' => now(),
                'removed_at'  => null,
                'is_primary'  => false,
            ]
        );
    }
}
