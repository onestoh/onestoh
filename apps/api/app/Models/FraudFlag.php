<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FraudFlag extends Model
{
    protected $fillable = ['user_id', 'ip_address', 'flag_type', 'risk_score', 'details', 'evidence', 'status', 'reviewed_by', 'resolution_notes', 'resolved_at'];
    protected $casts = ['evidence' => 'array', 'resolved_at' => 'datetime', 'risk_score' => 'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function isHighRisk(): bool { return $this->risk_score >= 75; }
}
