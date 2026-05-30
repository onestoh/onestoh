<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralConversion extends Model
{
    protected $guarded = [];

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
