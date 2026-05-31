<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PropertyPricingRule extends Model
{
    protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public static function getPrice(int $propertyId, string $date, ?int $roomId = null): ?float
    {
        $rule = static::where('property_id', $propertyId)
            ->when($roomId, fn($q) => $q->where(fn($q2) => $q2->where('room_id', $roomId)->orWhereNull('room_id')))
            ->where(fn($q) => $q
                ->where('day_of_week', Carbon::parse($date)->dayOfWeek)
                ->orWhere(fn($q2) => $q2->where('date_from', '<=', $date)->where('date_to', '>=', $date))
                ->orWhereNull('day_of_week')
            )
            ->orderByDesc('priority')
            ->first();

        return $rule ? (float) $rule->price_per_night : null;
    }
}
