<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Booking extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'asset_id', 'client_id', 'driver_id', 'broker_id', 'referral_code',
        'rental_type', 'duration_type', 'start_at', 'end_at', 'actual_end_at', 'status',
        'currency', 'base_amount', 'security_deposit_amount', 'driver_surcharge',
        'delivery_fee', 'insurance_fee', 'platform_fee', 'broker_commission', 'total_amount',
        'pickup_address', 'pickup_latitude', 'pickup_longitude',
        'delivery_address', 'delivery_latitude', 'delivery_longitude',
        'pre_rental_photos', 'post_rental_photos',
        'start_odometer', 'end_odometer',
        'cancellation_reason', 'cancelled_at',
        'agreement_pdf_path', 'agreement_signed_at',
        'client_notes', 'owner_notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'actual_end_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'agreement_signed_at' => 'datetime',
        'pre_rental_photos' => 'array',
        'post_rental_photos' => 'array',
        'base_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'broker_commission' => 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function driver() { return $this->belongsTo(User::class, 'driver_id'); }
    public function broker() { return $this->belongsTo(User::class, 'broker_id'); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function escrow() { return $this->hasOne(EscrowAccount::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function dispute() { return $this->hasOne(Dispute::class); }
    public function availabilitySlots() { return $this->hasMany(AvailabilitySlot::class); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isCompleted(): bool { return in_array($this->status, ['completed', 'closed']); }
    public function isCancelled(): bool { return str_starts_with($this->status, 'cancelled_'); }
}
