<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DataProductSubscription extends Model
{
    protected $fillable = ['data_product_id', 'subscriber_name', 'subscriber_email', 'subscriber_type', 'billing_cycle', 'amount_paid', 'api_key', 'current_period_ends_at', 'is_active'];
    protected $hidden = ['api_key'];
    protected $casts = ['current_period_ends_at' => 'datetime', 'is_active' => 'boolean', 'amount_paid' => 'decimal:2'];
    public function product() { return $this->belongsTo(DataProduct::class, 'data_product_id'); }
    public function isExpired(): bool { return $this->current_period_ends_at->isPast(); }
}
