<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'listing_id', 'client_id', 'operator_id', 'broker_id', 'booking_ref',
        'mode', 'duration_type', 'start_datetime', 'end_datetime',
        'pickup_location', 'delivery_address', 'base_amount', 'security_deposit',
        'driver_surcharge', 'delivery_fee', 'platform_fee', 'insurance_fee',
        'total_amount', 'status', 'slot_hold_expires_at', 'notes',
        'pre_photos', 'post_photos', 'started_at', 'completed_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'pre_photos' => 'array',
        'post_photos' => 'array',
        'slot_hold_expires_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_ref)) {
                $booking->booking_ref = 'BK-' . strtoupper(uniqid());
            }
        });
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }
}
