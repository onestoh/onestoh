<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'booking_id', 'raised_by', 'against_id', 'type', 'description',
        'status', 'evidence', 'admin_ruling', 'ruling_by', 'resolved_at',
        'refund_to_client', 'retain_by_owner',
    ];

    protected $casts = [
        'evidence' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function against()
    {
        return $this->belongsTo(User::class, 'against_id');
    }
}
