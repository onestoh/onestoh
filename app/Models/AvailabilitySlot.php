<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    protected $fillable = [
        'listing_id', 'date', 'hour_from', 'hour_to', 'status', 'booking_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
