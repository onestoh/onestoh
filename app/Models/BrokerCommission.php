<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerCommission extends Model
{
    protected $fillable = [
        'broker_id', 'booking_id', 'sale_id', 'commission_rate',
        'commission_amount', 'status', 'credited_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'credited_at' => 'datetime',
    ];

    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
