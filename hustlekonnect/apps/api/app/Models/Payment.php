<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'booking_id', 'user_id', 'gateway', 'gateway_reference',
        'idempotency_key', 'amount', 'currency', 'status',
        'gateway_response', 'paid_at', 'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'gateway_response' => 'array',
            'paid_at'          => 'datetime',
            'refunded_at'      => 'datetime',
        ];
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
