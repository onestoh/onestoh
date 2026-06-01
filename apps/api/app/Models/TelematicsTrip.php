<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TelematicsTrip extends Model
{
    protected $fillable = ['asset_id', 'booking_id', 'start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'started_at', 'ended_at', 'distance_km', 'max_speed_kmh', 'avg_speed_kmh', 'idle_seconds', 'harsh_braking_events', 'speeding_events', 'fuel_consumed_litres', 'driver_score', 'route_polyline'];
    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime', 'route_polyline' => 'array'];
    public function asset() { return $this->belongsTo(Asset::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
}
