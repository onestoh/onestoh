<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AiPricingSuggestion extends Model
{
    protected $fillable = ['asset_id', 'duration_type', 'current_rate', 'suggested_rate', 'market_avg', 'market_min', 'market_max', 'comparable_listings_count', 'recommendation', 'confidence_score', 'reasoning', 'owner_action', 'owner_applied_rate', 'expires_at'];
    protected $casts = ['reasoning' => 'array', 'expires_at' => 'datetime', 'suggested_rate' => 'decimal:2', 'market_avg' => 'decimal:2', 'confidence_score' => 'decimal:2'];
    public function asset() { return $this->belongsTo(Asset::class); }
    public function isExpired(): bool { return $this->expires_at->isPast(); }
    public function isActioned(): bool { return !is_null($this->owner_action); }
}
