<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinancingPartner extends Model
{
    protected $fillable = ['name', 'type', 'logo_url', 'country_code', 'min_loan_amount', 'max_loan_amount', 'min_interest_rate_pa', 'max_interest_rate_pa', 'min_tenure_months', 'max_tenure_months', 'min_deposit_pct', 'api_endpoint', 'api_key_encrypted', 'is_active', 'eligible_asset_categories'];
    protected $hidden = ['api_key_encrypted'];
    protected $casts = ['is_active' => 'boolean', 'eligible_asset_categories' => 'array', 'min_loan_amount' => 'decimal:2', 'max_loan_amount' => 'decimal:2'];
    public function applications() { return $this->hasMany(FinancingApplication::class); }
    public function monthlyPayment(float $principal, float $annualRate, int $months): float
    {
        $r = ($annualRate / 100) / 12;
        return $r > 0 ? round($principal * $r * pow(1 + $r, $months) / (pow(1 + $r, $months) - 1), 2) : round($principal / $months, 2);
    }
}
