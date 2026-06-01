<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AssetAnalyticsCache extends Model
{
    protected $fillable = [
        'asset_id', 'revenue_this_month', 'revenue_last_month', 'revenue_this_year',
        'utilisation_rate_30d', 'utilisation_rate_90d', 'bookings_this_month',
        'bookings_last_month', 'avg_booking_duration_days', 'idle_days_last_30',
        'maintenance_cost_ytd', 'net_margin_this_month', 'last_calculated_at',
    ];

    protected $casts = [
        'last_calculated_at'   => 'datetime',
        'revenue_this_month'   => 'decimal:2',
        'revenue_this_year'    => 'decimal:2',
        'utilisation_rate_30d' => 'decimal:2',
        'net_margin_this_month'=> 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
}
