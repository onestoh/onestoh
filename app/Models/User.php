<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','role','phone','national_id','status',
        'trust_score','verification_tier','referral_code','referred_by','avatar',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(Str::random(8));
            }
        });
        static::created(function (User $user) {
            Wallet::create(['user_id' => $user->id]);
        });
    }

    public function isAdmin(): bool { return $this->role === 'super_admin'; }
    public function isYardOwner(): bool { return in_array($this->role, ['yard_owner','individual_owner']); }
    public function isBroker(): bool { return $this->role === 'broker'; }
    public function isClient(): bool { return $this->role === 'client'; }
    public function isOperator(): bool { return $this->role === 'operator'; }
    public function isVerified(): bool { return $this->status === 'verified'; }

    public function yard() { return $this->hasOne(Yard::class); }
    public function listings() { return $this->hasMany(Listing::class); }
    public function clientBookings() { return $this->hasMany(Booking::class, 'client_id'); }
    public function operatorBookings() { return $this->hasMany(Booking::class, 'operator_id'); }
    public function wallet() { return $this->hasOne(Wallet::class); }
    public function kycDocuments() { return $this->hasMany(KycDocument::class); }
    public function reviewsGiven() { return $this->hasMany(Review::class, 'reviewer_id'); }
    public function reviewsReceived() { return $this->hasMany(Review::class, 'reviewee_id'); }
    public function brokerCommissions() { return $this->hasMany(BrokerCommission::class, 'broker_id'); }
    public function payoutRequests() { return $this->hasMany(PayoutRequest::class); }
    public function referrer() { return $this->belongsTo(User::class, 'referred_by'); }
    public function referrals() { return $this->hasMany(User::class, 'referred_by'); }
    public function sentMessages() { return $this->hasMany(Message::class, 'from_user_id'); }
    public function receivedMessages() { return $this->hasMany(Message::class, 'to_user_id'); }
}
