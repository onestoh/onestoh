<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'listing_id', 'service_type', 'cost', 'service_centre',
        'service_date', 'next_due_date', 'next_due_km', 'notes',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'service_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
