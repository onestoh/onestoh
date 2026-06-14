<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Yard extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'logo', 'phone', 'email',
        'county', 'city', 'address', 'latitude', 'longitude',
        'business_reg_number', 'kra_pin', 'ntsa_cert', 'status',
        'verification_tier', 'total_ratings', 'rating_count', 'average_rating',
    ];

    protected $casts = [
        'average_rating' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
