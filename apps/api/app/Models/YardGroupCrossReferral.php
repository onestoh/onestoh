<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class YardGroupCrossReferral extends Model
{
    protected $table = 'yardgroup_cross_referrals';
    protected $fillable = ['broker_id', 'referral_code', 'source_platform', 'target_platform', 'referred_user_id', 'transaction_id', 'commission_earned', 'status'];
    protected $casts = ['commission_earned' => 'decimal:2'];
    public function broker() { return $this->belongsTo(User::class, 'broker_id'); }
}
