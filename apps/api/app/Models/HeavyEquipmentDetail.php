<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HeavyEquipmentDetail extends Model
{
    protected $fillable = [
        'asset_id', 'machine_class', 'operating_weight', 'bucket_blade_capacity',
        'fuel_consumption_per_hour', 'transport_method', 'mobilisation_cost',
        'demobilisation_cost', 'minimum_hire_days', 'max_daily_hours',
        'site_access_requirements', 'operator_cert_required', 'fuel_provisioning',
        'owner_review_required', 'early_termination_fee_pct', 'roadworthiness_cert_path',
        'roadworthiness_expires_at',
    ];

    protected $casts = [
        'roadworthiness_expires_at'  => 'date',
        'owner_review_required'      => 'boolean',
        'mobilisation_cost'          => 'decimal:2',
        'demobilisation_cost'        => 'decimal:2',
        'early_termination_fee_pct'  => 'decimal:2',
        'fuel_consumption_per_hour'  => 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
}
