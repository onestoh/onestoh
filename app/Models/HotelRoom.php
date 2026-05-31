<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'images'    => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
