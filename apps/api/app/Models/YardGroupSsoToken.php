<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class YardGroupSsoToken extends Model
{
    protected $table = 'yardgroup_sso_tokens';
    protected $fillable = ['user_id', 'platform', 'platform_user_id', 'sso_token', 'shared_profile', 'expires_at'];
    protected $casts = ['shared_profile' => 'array', 'expires_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function isExpired(): bool { return $this->expires_at->isPast(); }
}
