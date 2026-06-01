<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id', 'name', 'description', 'logo_url',
        'county', 'location_area', 'latitude', 'longitude',
        'phone', 'email', 'verification_tier', 'tier_expires_at', 'is_active',
    ];

    protected $casts = [
        'tier_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function assets() { return $this->hasMany(Asset::class); }
    public function drivers() { return $this->hasMany(Driver::class, 'primary_yard_id'); }
}
