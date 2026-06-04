<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'yard_id', 'tenant_id', 'title', 'description', 'category',
        'make', 'model', 'year', 'color', 'plate_number', 'vin',
        'daily_rate_kes', 'weekly_rate_kes', 'monthly_rate_kes', 'deposit_kes',
        'pricing_currency', 'location_city', 'location_country',
        'latitude', 'longitude', 'seats', 'fuel_type', 'transmission',
        'mileage_km', 'features', 'is_for_sale', 'sale_price_kes',
        'is_active', 'is_available', 'is_featured', 'photo_hashes',
    ];

    protected function casts(): array
    {
        return [
            'features'     => 'array',
            'photo_hashes' => 'array',
            'is_for_sale'  => 'boolean',
            'is_active'    => 'boolean',
            'is_available' => 'boolean',
            'is_featured'  => 'boolean',
        ];
    }

    public function yard()     { return $this->belongsTo(Yard::class); }
    public function media()    { return $this->hasMany(AssetMedia::class)->orderBy('sort_order'); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function primaryImage(): ?string { return $this->media()->where('is_primary', true)->value('url'); }

    public function isAvailableFor(string $start, string $end): bool
    {
        return !$this->bookings()
            ->whereIn('status', ['confirmed','owner_notified','client_prepared','active'])
            ->where(fn($q) => $q->whereBetween('start_date', [$start, $end])->orWhereBetween('end_date', [$start, $end])->orWhere(fn($q2) => $q2->where('start_date', '<=', $start)->where('end_date', '>=', $end)))
            ->exists();
    }
}
