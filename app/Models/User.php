<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Role helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isLandlord(): bool { return $this->role === 'landlord'; }
    public function isTenant(): bool { return $this->role === 'tenant'; }
    public function isBroker(): bool { return in_array($this->role, ['broker_licensed', 'broker_unlicensed']); }
    public function isDeveloper(): bool { return $this->role === 'developer'; }
    public function isValuer(): bool { return $this->role === 'valuer'; }
    public function isSurveyor(): bool { return $this->role === 'surveyor'; }
    public function isAuctioneer(): bool { return $this->role === 'auctioneer'; }
    public function isInvestor(): bool { return $this->role === 'investor'; }
    public function isPropertyManager(): bool { return $this->role === 'property_manager'; }
    public function isFinance(): bool { return $this->role === 'finance'; }
    public function isCorporate(): bool { return $this->role === 'corporate'; }

    // Relationships
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    public function leasesAsLandlord()
    {
        return $this->hasMany(Lease::class, 'landlord_id');
    }

    public function rentPayments()
    {
        return $this->hasMany(RentPayment::class, 'tenant_id');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationLog::class);
    }
}
