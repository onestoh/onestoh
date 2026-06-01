<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'yard_id', 'owner_id', 'category', 'sub_type', 'make', 'model', 'year',
        'registration_plate_encrypted', 'vin_encrypted', 'fuel_type', 'transmission',
        'seats', 'engine_size', 'load_capacity', 'status', 'listing_mode',
        'is_self_drive_enabled', 'is_chauffeur_enabled', 'is_delivery_enabled', 'delivery_fee_per_km',
        'hourly_rate', 'minimum_hours', 'daily_rate', 'weekly_rate', 'monthly_rate',
        'security_deposit', 'mileage_cap_per_day', 'mileage_overage_per_km', 'overtime_per_hour',
        'pickup_county', 'pickup_area', 'pickup_latitude', 'pickup_longitude',
        'features', 'usage_rules', 'description',
        'sale_price', 'sale_description',
        'is_published', 'admin_approved_at', 'approved_by',
    ];

    protected $casts = [
        'features' => 'array',
        'photo_hashes' => 'array',
        'is_self_drive_enabled' => 'boolean',
        'is_chauffeur_enabled' => 'boolean',
        'is_delivery_enabled' => 'boolean',
        'is_published' => 'boolean',
        'admin_approved_at' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    protected $hidden = ['registration_plate_encrypted', 'vin_encrypted', 'photo_hashes'];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function yard() { return $this->belongsTo(Yard::class); }
    public function media() { return $this->hasMany(AssetMedia::class)->orderBy('sort_order'); }
    public function primaryPhoto() { return $this->hasOne(AssetMedia::class)->where('is_primary', true); }
    public function availabilitySlots() { return $this->hasMany(AvailabilitySlot::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function reviews() { return $this->hasManyThrough(Review::class, Booking::class, 'asset_id', 'booking_id', 'id', 'id'); }

    public function hasMinimumPhotos(): bool
    {
        return $this->media()->where('type', 'photo')->count() >= 5;
    }

    public function hasActiveRate(): bool
    {
        return $this->hourly_rate || $this->daily_rate || $this->weekly_rate || $this->monthly_rate;
    }
}
