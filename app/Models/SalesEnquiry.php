<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesEnquiry extends Model
{
    protected $fillable = [
        'listing_id', 'client_id', 'broker_id', 'offered_price',
        'owner_counter', 'agreed_price', 'reservation_fee', 'status',
        'reservation_expires_at',
    ];

    protected $casts = [
        'offered_price' => 'decimal:2',
        'owner_counter' => 'decimal:2',
        'agreed_price' => 'decimal:2',
        'reservation_fee' => 'decimal:2',
        'reservation_expires_at' => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }
}
