<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CorporateMember extends Model
{
    protected $fillable = [
        'corporate_account_id', 'user_id', 'role',
        'individual_spend_limit', 'can_approve_bookings', 'is_active',
    ];

    protected $casts = [
        'individual_spend_limit' => 'decimal:2',
        'can_approve_bookings'   => 'boolean',
        'is_active'              => 'boolean',
    ];

    public function corporateAccount() { return $this->belongsTo(CorporateAccount::class); }
    public function user()             { return $this->belongsTo(User::class); }
}
