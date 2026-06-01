<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralClick extends Model
{
    protected $fillable = ['referral_code', 'broker_id', 'asset_id', 'ip_address', 'user_agent', 'converted_booking_id', 'converted_at'];
    protected $casts    = ['converted_at' => 'datetime'];

    public function broker() { return $this->belongsTo(User::class, 'broker_id'); }
    public function asset()  { return $this->belongsTo(Asset::class); }
    public function booking() { return $this->belongsTo(Booking::class, 'converted_booking_id'); }
}
