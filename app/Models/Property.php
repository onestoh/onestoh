<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'images' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Accessors
    public function getPriceFormattedAttribute(): string
    {
        return 'KES ' . number_format($this->price, 0);
    }

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function escrowTransactions()
    {
        return $this->hasMany(EscrowTransaction::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function saves()
    {
        return $this->hasMany(PropertySave::class);
    }

    public function documents()
    {
        return $this->hasMany(PropertyDocument::class);
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function hotelRooms()
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function pricingRules()
    {
        return $this->hasMany(PropertyPricingRule::class);
    }

    public function availabilities()
    {
        return $this->hasMany(PropertyAvailability::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookingReview::class);
    }
}
