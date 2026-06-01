<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinancingApplication extends Model
{
    protected $fillable = ['user_id', 'asset_id', 'financing_partner_id', 'type', 'asset_value', 'requested_amount', 'deposit_amount', 'tenure_months', 'interest_rate_pa', 'monthly_repayment', 'status', 'partner_reference', 'submitted_documents', 'rejection_reason', 'submitted_at', 'decision_at', 'disbursed_at'];
    protected $casts = ['submitted_documents' => 'array', 'submitted_at' => 'datetime', 'decision_at' => 'datetime', 'disbursed_at' => 'datetime', 'asset_value' => 'decimal:2', 'requested_amount' => 'decimal:2', 'monthly_repayment' => 'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function asset() { return $this->belongsTo(Asset::class); }
    public function partner() { return $this->belongsTo(FinancingPartner::class, 'financing_partner_id'); }
    public function leaseAgreement() { return $this->hasOne(LeaseToOwnAgreement::class); }
}
