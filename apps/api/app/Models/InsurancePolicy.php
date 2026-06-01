<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'booking_id', 'product_type', 'insurer', 'policy_number', 'premium',
        'coverage_limit', 'excess_amount', 'valid_from', 'valid_to',
        'status', 'certificate_path',
    ];

    protected $casts = [
        'valid_from'      => 'date',
        'valid_to'        => 'date',
        'premium'         => 'decimal:2',
        'coverage_limit'  => 'decimal:2',
        'excess_amount'   => 'decimal:2',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function claims()  { return $this->hasMany(InsuranceClaim::class); }

    public function isExpired(): bool { return $this->valid_to->isPast(); }
}
