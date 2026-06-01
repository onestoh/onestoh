<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ListingPromotion extends Model
{
    protected $fillable = [
        'asset_id', 'owner_id', 'type', 'amount_paid', 'starts_at', 'ends_at',
        'status', 'impressions', 'clicks', 'bookings_from_promo', 'payment_reference',
    ];

    protected $casts = [
        'starts_at'   => 'date',
        'ends_at'     => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }
}
