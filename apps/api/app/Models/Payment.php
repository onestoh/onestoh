<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'booking_id', 'user_id', 'gateway', 'type', 'amount', 'currency', 'status',
        'gateway_reference', 'gateway_checkout_id', 'phone_number',
        'gateway_response', 'paid_at', 'receipt_pdf_path', 'idempotency_key',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $hidden = ['gateway_response'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function user() { return $this->belongsTo(User::class); }
}
