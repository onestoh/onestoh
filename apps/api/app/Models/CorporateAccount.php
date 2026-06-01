<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CorporateAccount extends Model
{
    protected $fillable = [
        'owner_id', 'company_name', 'kra_pin', 'registration_number',
        'billing_email', 'billing_address', 'monthly_spend_limit',
        'per_booking_limit', 'requires_approval', 'is_active', 'po_reference_format',
    ];

    protected $casts = [
        'monthly_spend_limit' => 'decimal:2',
        'per_booking_limit'   => 'decimal:2',
        'requires_approval'   => 'boolean',
        'is_active'           => 'boolean',
    ];

    public function owner()         { return $this->belongsTo(User::class, 'owner_id'); }
    public function members()       { return $this->hasMany(CorporateMember::class); }
    public function bookings()      { return $this->hasMany(Booking::class); }
    public function activeMembers() { return $this->members()->where('is_active', true); }
}
