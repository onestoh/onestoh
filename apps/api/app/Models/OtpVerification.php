<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = ['user_id', 'type', 'otp', 'target', 'expires_at', 'verified_at', 'attempts'];
    protected $casts    = ['expires_at' => 'datetime', 'verified_at' => 'datetime'];
    protected $hidden   = ['otp'];

    public function user() { return $this->belongsTo(User::class); }
}
