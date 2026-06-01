<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DemandForecast extends Model
{
    protected $fillable = ['category', 'county', 'forecast_date', 'period_type', 'predicted_demand_index', 'predicted_booking_count', 'confidence_score', 'demand_drivers'];
    protected $casts = ['forecast_date' => 'date', 'demand_drivers' => 'array', 'predicted_demand_index' => 'decimal:4', 'confidence_score' => 'decimal:2'];
    public function isHighDemand(): bool { return $this->predicted_demand_index > 1.3; }
}
