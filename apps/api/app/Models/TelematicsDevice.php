<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TelematicsDevice extends Model
{
    protected $fillable = ['asset_id', 'device_id', 'device_type', 'sim_iccid', 'firmware_version', 'is_active', 'last_ping_at'];
    protected $casts = ['is_active' => 'boolean', 'last_ping_at' => 'datetime'];
    public function asset() { return $this->belongsTo(Asset::class); }
    public function pings() { return $this->hasMany(TelematicsPing::class, 'device_id', 'device_id'); }
    public function trips() { return $this->hasMany(TelematicsTrip::class, 'asset_id', 'asset_id'); }
    public function latestPing() { return $this->hasOne(TelematicsPing::class, 'device_id', 'device_id')->latestOfMany('pinged_at'); }
    public function isOnline(): bool { return $this->last_ping_at && $this->last_ping_at->diffInMinutes(now()) < 5; }
}
