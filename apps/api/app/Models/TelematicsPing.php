<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TelematicsPing extends Model
{
    protected $fillable = ['asset_id', 'device_id', 'latitude', 'longitude', 'speed_kmh', 'heading', 'odometer_km', 'fuel_level_pct', 'ignition_on', 'engine_on', 'battery_voltage', 'alerts', 'pinged_at'];
    protected $casts = ['ignition_on' => 'boolean', 'engine_on' => 'boolean', 'alerts' => 'array', 'pinged_at' => 'datetime', 'latitude' => 'float', 'longitude' => 'float'];
    public function asset() { return $this->belongsTo(Asset::class); }
}
