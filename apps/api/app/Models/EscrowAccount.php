<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscrowAccount extends Model
{
    protected $fillable = [
        'booking_id', 'total_collected', 'rental_fee', 'security_deposit',
        'platform_fee', 'broker_commission', 'insurance_fee',
        'status', 'release_trigger', 'released_by', 'released_at', 'admin_notes',
    ];

    protected $casts = [
        'released_at' => 'datetime',
        'total_collected' => 'decimal:2',
        'rental_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'broker_commission' => 'decimal:2',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function releasedBy() { return $this->belongsTo(User::class, 'released_by'); }
}
