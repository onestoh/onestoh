<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    protected $fillable = ['asset_id', 'date', 'hour', 'status', 'booking_id', 'hold_expires_at'];
    protected $casts = [
        'date' => 'date',
        'hold_expires_at' => 'datetime',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function booking() { return $this->belongsTo(Booking::class); }

    public function isHoldExpired(): bool
    {
        return $this->status === 'pending' && $this->hold_expires_at && $this->hold_expires_at->isPast();
    }
}
