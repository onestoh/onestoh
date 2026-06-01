<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'kyc_status', 'trust_score', 'referral_code', 'referred_by',
        'national_id_number', 'date_of_birth', 'county', 'address',
        'is_licensed_broker', 'company_name', 'kra_pin',
        'kyc_submitted_at', 'kyc_reviewed_at', 'kyc_reviewed_by', 'kyc_rejection_reason',
    ];

    protected $hidden = ['password', 'remember_token', 'national_id_number'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'kyc_submitted_at' => 'datetime',
        'kyc_reviewed_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_licensed_broker' => 'boolean',
    ];

    public function yards() { return $this->hasMany(Yard::class, 'owner_id'); }
    public function assets() { return $this->hasMany(Asset::class, 'owner_id'); }
    public function bookingsAsClient() { return $this->hasMany(Booking::class, 'client_id'); }
    public function wallet() { return $this->hasOne(Wallet::class); }
    public function kycDocuments() { return $this->hasMany(KycDocument::class); }
    public function driver() { return $this->hasOne(Driver::class); }
    public function notifications() { return $this->hasMany(PlatformNotification::class); }

    public function isAdmin(): bool { return $this->role === 'super_admin'; }
    public function isOwner(): bool { return in_array($this->role, ['yard_owner', 'individual_owner']); }
    public function isKycApproved(): bool { return $this->kyc_status === 'approved'; }

    public function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12));
        } while (self::where('referral_code', $code)->exists());
        return $code;
    }
}
