<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'user_id', 'asset_id', 'tenant_id', 'broker_id', 'driver_id',
        'corporate_account_id', 'status', 'start_date', 'end_date',
        'total_days', 'daily_rate_kes', 'subtotal_kes', 'insurance_fee_kes',
        'driver_fee_kes', 'platform_fee_kes', 'discount_kes', 'total_amount_kes',
        'currency', 'pickup_location', 'dropoff_location', 'insurance_type',
        'with_driver', 'notes', 'cancellation_reason',
        'confirmed_at', 'activated_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'datetime',
            'end_date'      => 'datetime',
            'confirmed_at'  => 'datetime',
            'activated_at'  => 'datetime',
            'completed_at'  => 'datetime',
            'with_driver'   => 'boolean',
        ];
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function asset()   { return $this->belongsTo(Asset::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function escrow()  { return $this->hasOne(EscrowAccount::class); }
    public function disputes() { return $this->hasMany(Dispute::class); }
    public function driver()  { return $this->belongsTo(Driver::class); }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'completed')->exists();
    }

    public function hasOpenDispute(): bool
    {
        return $this->disputes()->where('status', 'open')->exists();
    }
}
