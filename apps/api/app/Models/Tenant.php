<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'domain', 'logo_url', 'primary_color', 'country_code', 'default_currency', 'timezone', 'active_payment_gateways', 'platform_fee_pct', 'referral_commission_pct', 'is_active', 'plan', 'monthly_fee', 'per_transaction_fee_pct', 'trial_ends_at'];
    protected $casts = ['active_payment_gateways' => 'array', 'is_active' => 'boolean', 'trial_ends_at' => 'datetime', 'platform_fee_pct' => 'decimal:2', 'monthly_fee' => 'decimal:2'];
    public function admins() { return $this->hasMany(TenantAdmin::class); }
    public function users() { return $this->hasMany(User::class); }
    public function assets() { return $this->hasMany(Asset::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function isOnTrial(): bool { return $this->trial_ends_at && $this->trial_ends_at->isFuture(); }
    public function monthlyRevenue(): float { return $this->bookings()->whereMonth('created_at', now()->month)->where('status', 'closed')->sum('total_amount') * ($this->per_transaction_fee_pct / 100); }
}
