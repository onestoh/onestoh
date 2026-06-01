<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeaseToOwnAgreement extends Model
{
    protected $fillable = ['financing_application_id', 'asset_id', 'lessee_id', 'total_lease_value', 'monthly_payment', 'total_months', 'months_paid', 'balloon_payment', 'residual_value', 'start_date', 'end_date', 'next_payment_due', 'status', 'ownership_transferred', 'ownership_transferred_at'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'next_payment_due' => 'date', 'ownership_transferred' => 'boolean', 'ownership_transferred_at' => 'datetime', 'total_lease_value' => 'decimal:2', 'monthly_payment' => 'decimal:2'];
    public function application() { return $this->belongsTo(FinancingApplication::class, 'financing_application_id'); }
    public function asset() { return $this->belongsTo(Asset::class); }
    public function lessee() { return $this->belongsTo(User::class, 'lessee_id'); }
    public function remainingAmount(): float { return ($this->total_months - $this->months_paid) * $this->monthly_payment + $this->balloon_payment; }
    public function completionPct(): float { return round(($this->months_paid / $this->total_months) * 100, 1); }
}
