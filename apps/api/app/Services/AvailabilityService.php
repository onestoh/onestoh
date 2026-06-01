<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\AvailabilitySlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    const HOLD_MINUTES = 15;

    /**
     * Get availability calendar for an asset for a date range.
     * Returns array keyed by date with status per slot.
     */
    public function getCalendar(Asset $asset, Carbon $from, Carbon $to): array
    {
        $slots = AvailabilitySlot::where('asset_id', $asset->id)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->groupBy(fn($s) => $s->date->toDateString());

        $calendar = [];
        $period   = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            $dateStr    = $date->toDateString();
            $daySlots   = $slots->get($dateStr, collect());

            // Release any expired holds first
            $expiredIds = $daySlots->filter(fn($s) => $s->isHoldExpired())->pluck('id');
            if ($expiredIds->isNotEmpty()) {
                AvailabilitySlot::whereIn('id', $expiredIds)->update([
                    'status'          => 'available',
                    'booking_id'      => null,
                    'hold_expires_at' => null,
                ]);
                // Refresh
                $daySlots = AvailabilitySlot::where('asset_id', $asset->id)
                    ->where('date', $dateStr)
                    ->get();
            }

            $calendar[$dateStr] = $this->summarizeDay($asset, $daySlots);
        }

        return $calendar;
    }

    /**
     * Hold availability slots for a booking period.
     * Throws exception if slots are unavailable.
     */
    public function holdSlots(Asset $asset, Carbon $startAt, Carbon $endAt): Collection
    {
        return DB::transaction(function () use ($asset, $startAt, $endAt) {
            $holdExpiry = now()->addMinutes(self::HOLD_MINUTES);
            $heldSlots  = collect();

            $period = CarbonPeriod::create($startAt->toDateString(), $endAt->subDay()->toDateString());

            foreach ($period as $date) {
                $dateStr = $date->toDateString();

                // Check for conflicting slots
                $conflict = AvailabilitySlot::where('asset_id', $asset->id)
                    ->where('date', $dateStr)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->exists();

                if ($conflict) {
                    throw new \DomainException("Asset is not available on {$dateStr}.");
                }

                // Create or update to pending
                $slot = AvailabilitySlot::updateOrCreate(
                    ['asset_id' => $asset->id, 'date' => $dateStr, 'hour' => null],
                    ['status' => 'pending', 'hold_expires_at' => $holdExpiry]
                );

                $heldSlots->push($slot);
            }

            return $heldSlots;
        });
    }

    /**
     * Release expired holds for all assets.
     */
    public function releaseExpiredHolds(): int
    {
        return AvailabilitySlot::where('status', 'pending')
            ->where('hold_expires_at', '<', now())
            ->update([
                'status'          => 'available',
                'booking_id'      => null,
                'hold_expires_at' => null,
            ]);
    }

    /**
     * Block dates for owner maintenance / personal use.
     */
    public function blockDates(Asset $asset, Carbon $from, Carbon $to, string $reason = 'owner_blocked'): void
    {
        $period = CarbonPeriod::create($from->toDateString(), $to->toDateString());
        foreach ($period as $date) {
            AvailabilitySlot::updateOrCreate(
                ['asset_id' => $asset->id, 'date' => $date->toDateString(), 'hour' => null],
                ['status' => 'owner_blocked', 'hold_expires_at' => null, 'booking_id' => null]
            );
        }
    }

    /**
     * Unblock dates.
     */
    public function unblockDates(Asset $asset, Carbon $from, Carbon $to): void
    {
        AvailabilitySlot::where('asset_id', $asset->id)
            ->where('status', 'owner_blocked')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->delete();
    }

    /**
     * Check if an asset is available for a date range.
     */
    public function isAvailable(Asset $asset, Carbon $startAt, Carbon $endAt): bool
    {
        $period = CarbonPeriod::create($startAt->toDateString(), $endAt->subDay()->toDateString());
        foreach ($period as $date) {
            $blocked = AvailabilitySlot::where('asset_id', $asset->id)
                ->where('date', $date->toDateString())
                ->whereIn('status', ['pending', 'confirmed', 'owner_blocked'])
                ->exists();
            if ($blocked) return false;
        }
        return true;
    }

    private function summarizeDay(Asset $asset, Collection $slots): array
    {
        if ($slots->isEmpty()) {
            return ['status' => 'available', 'slots' => []];
        }

        $hasConfirmed    = $slots->contains('status', 'confirmed');
        $hasPending      = $slots->contains('status', 'pending');
        $hasOwnerBlocked = $slots->contains('status', 'owner_blocked');

        $overallStatus = match (true) {
            $hasConfirmed    => 'booked',
            $hasOwnerBlocked => 'blocked',
            $hasPending      => 'pending',
            default          => 'available',
        };

        return [
            'status' => $overallStatus,
            'slots'  => $slots->map(fn($s) => [
                'hour'   => $s->hour,
                'status' => $s->status,
            ])->values()->toArray(),
        ];
    }
}
