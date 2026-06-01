<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    protected $fillable = ['user_id', 'name', 'client_id', 'client_secret_hash', 'scopes', 'allowed_ips', 'webhook_url', 'webhook_secret', 'is_active', 'rate_limit_per_minute', 'total_requests', 'last_used_at'];
    protected $hidden = ['client_secret_hash', 'webhook_secret'];
    protected $casts = ['scopes' => 'array', 'allowed_ips' => 'array', 'is_active' => 'boolean', 'last_used_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function webhookDeliveries() { return $this->hasMany(ApiWebhookDelivery::class); }
    public function hasScope(string $scope): bool { return in_array($scope, $this->scopes ?? []); }
}
