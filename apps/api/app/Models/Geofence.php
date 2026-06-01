<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    protected $fillable = ['asset_id', 'name', 'type', 'center_latitude', 'center_longitude', 'radius_meters', 'polygon_coordinates', 'alert_on_entry', 'alert_on_exit', 'is_active'];
    protected $casts = ['polygon_coordinates' => 'array', 'alert_on_entry' => 'boolean', 'alert_on_exit' => 'boolean', 'is_active' => 'boolean'];
    public function asset() { return $this->belongsTo(Asset::class); }

    public function containsPoint(float $lat, float $lng): bool
    {
        if ($this->type === 'circle') {
            $dist = $this->haversineDistance($lat, $lng, $this->center_latitude, $this->center_longitude);
            return $dist <= $this->radius_meters;
        }
        return $this->pointInPolygon($lat, $lng, $this->polygon_coordinates);
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000;
        $phi1 = deg2rad($lat1); $phi2 = deg2rad($lat2);
        $dphi = deg2rad($lat2 - $lat1); $dlambda = deg2rad($lng2 - $lng1);
        $a = sin($dphi/2)**2 + cos($phi1)*cos($phi2)*sin($dlambda/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    private function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $inside = false; $n = count($polygon);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            if ((($polygon[$i][1] > $lng) !== ($polygon[$j][1] > $lng)) &&
                ($lat < ($polygon[$j][0] - $polygon[$i][0]) * ($lng - $polygon[$i][1]) / ($polygon[$j][1] - $polygon[$i][1]) + $polygon[$i][0])) {
                $inside = !$inside;
            }
        }
        return $inside;
    }
}
