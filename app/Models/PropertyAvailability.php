<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAvailability extends Model
{
    protected $table = 'property_availability';
    protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public static function isAvailable(int $propertyId, string $checkIn, string $checkOut): bool
    {
        $dates = [];
        $current = \Carbon\Carbon::parse($checkIn);
        $end     = \Carbon\Carbon::parse($checkOut);
        while ($current->lt($end)) {
            $dates[] = $current->toDateString();
            $current->addDay();
        }

        return static::where('property_id', $propertyId)
            ->whereIn('blocked_date', $dates)
            ->doesntExist();
    }
}
