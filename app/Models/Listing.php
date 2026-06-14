<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'yard_id', 'category_id', 'title', 'slug', 'description',
        'asset_type', 'make', 'model', 'year', 'registration_plate',
        'engine_size', 'fuel_type', 'transmission', 'seats', 'load_capacity',
        'listing_mode', 'status', 'hourly_rate', 'daily_rate', 'weekly_rate',
        'monthly_rate', 'min_hourly', 'security_deposit', 'driver_surcharge_daily',
        'delivery_fee_per_km', 'mileage_cap', 'overtime_charge', 'drive_mode',
        'sale_price', 'county', 'city', 'area', 'latitude', 'longitude',
        'features', 'insurance_cert', 'ntsa_sticker_date', 'vin_number',
        'is_featured', 'average_rating', 'rating_count', 'view_count',
    ];

    protected $casts = [
        'features' => 'array',
        'ntsa_sticker_date' => 'date',
        'is_featured' => 'boolean',
        'average_rating' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yard()
    {
        return $this->belongsTo(Yard::class);
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function photos()
    {
        return $this->hasMany(ListingPhoto::class);
    }

    public function availabilitySlots()
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForRent($query)
    {
        return $query->whereIn('listing_mode', ['rental', 'both']);
    }

    public function scopeForSale($query)
    {
        return $query->whereIn('listing_mode', ['sale', 'both']);
    }
}
