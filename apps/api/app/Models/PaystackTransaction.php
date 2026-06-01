<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaystackTransaction extends Model
{
    protected $fillable = ['booking_id', 'user_id', 'reference', 'access_code', 'amount_kobo', 'currency', 'status', 'gateway_response', 'paid_at'];
    protected $casts = ['gateway_response' => 'array', 'paid_at' => 'datetime'];
    public function booking() { return $this->belongsTo(Booking::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function amountInCurrency(): float { return $this->amount_kobo / 100; }
}
