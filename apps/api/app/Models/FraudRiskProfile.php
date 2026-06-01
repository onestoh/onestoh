<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FraudRiskProfile extends Model
{
    protected $fillable = ['user_id', 'overall_risk_score', 'flags_count', 'confirmed_fraud_count', 'last_known_ip', 'device_fingerprint', 'ip_history', 'risk_factors', 'last_assessed_at'];
    protected $casts = ['ip_history' => 'array', 'risk_factors' => 'array', 'last_assessed_at' => 'datetime', 'overall_risk_score' => 'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function isHighRisk(): bool { return $this->overall_risk_score >= 70; }
    public function isMediumRisk(): bool { return $this->overall_risk_score >= 40 && $this->overall_risk_score < 70; }
}
