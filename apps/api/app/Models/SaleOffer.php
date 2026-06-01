<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SaleOffer extends Model
{
    protected $fillable = [
        'asset_id', 'buyer_id', 'seller_id', 'asking_price', 'offer_price',
        'agreed_price', 'reservation_fee', 'status', 'payment_deadline',
        'agreement_pdf_path', 'agreement_signed_at', 'notes',
    ];

    protected $casts = [
        'asking_price'        => 'decimal:2',
        'offer_price'         => 'decimal:2',
        'agreed_price'        => 'decimal:2',
        'reservation_fee'     => 'decimal:2',
        'payment_deadline'    => 'date',
        'agreement_signed_at' => 'datetime',
    ];

    public function asset()  { return $this->belongsTo(Asset::class); }
    public function buyer()  { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
}
