<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'kyc_status',
        'country', 'preferred_currency', 'trust_score', 'referral_code',
        'referred_by', 'tenant_id', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function yards() { return $this->hasMany(Yard::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function wallet() { return $this->hasOne(Wallet::class); }
    public function kycDocuments() { return $this->hasMany(KycDocument::class); }
    public function notifications() { return $this->hasMany(PlatformNotification::class); }
    public function driver() { return $this->hasOne(Driver::class); }

    public function isAdmin(): bool { return in_array($this->role, ['admin', 'superadmin']); }
    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isKycApproved(): bool { return $this->kyc_status === 'approved'; }

    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet ?? Wallet::create(['user_id' => $this->id, 'balance' => 0, 'currency' => $this->preferred_currency ?? 'KES']);
    }
}
