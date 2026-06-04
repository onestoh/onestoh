<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleOffer extends Model
{
    use HasUuids;

    protected $fillable = [
        'id','asset_id','seller_id','buyer_id','asking_price',
        'offered_price','final_price','currency','status',
        'inspection_date','sale_agreement_path','notes',
        'counter_offers',
    ];

    protected $casts = [
        'asking_price'    => 'float',
        'offered_price'   => 'float',
        'final_price'     => 'float',
        'counter_offers'  => 'array',
        'inspection_date' => 'datetime',
    ];

    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
}
