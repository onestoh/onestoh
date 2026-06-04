<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowAccount extends Model
{
    protected $fillable = [
        'booking_id','total_amount','platform_fee','owner_amount',
        'currency','status','held_at','released_at','dispute_id',
    ];

    protected $casts = [
        'total_amount'  => 'float',
        'platform_fee'  => 'float',
        'owner_amount'  => 'float',
        'held_at'       => 'datetime',
        'released_at'   => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function isHeld(): bool
    {
        return $this->status === 'held';
    }

    public function release(): void
    {
        $this->update(['status' => 'released', 'released_at' => now()]);
    }
}
