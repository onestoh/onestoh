<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DataProduct extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'type', 'audience', 'price_monthly', 'price_annual', 'api_endpoint', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'price_monthly' => 'decimal:2', 'price_annual' => 'decimal:2'];
    public function subscriptions() { return $this->hasMany(DataProductSubscription::class); }
    public function activeSubscriptions() { return $this->subscriptions()->where('is_active', true); }
}
