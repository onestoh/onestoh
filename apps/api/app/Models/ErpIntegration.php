<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ErpIntegration extends Model
{
    protected $fillable = ['user_id', 'platform', 'access_token_encrypted', 'refresh_token_encrypted', 'realm_id', 'tenant_id_erp', 'sync_settings', 'token_expires_at', 'last_synced_at', 'records_synced', 'is_active', 'last_error'];
    protected $hidden = ['access_token_encrypted', 'refresh_token_encrypted'];
    protected $casts = ['sync_settings' => 'array', 'token_expires_at' => 'datetime', 'last_synced_at' => 'datetime', 'is_active' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function syncLogs() { return $this->hasMany(ErpSyncLog::class); }
    public function isTokenExpired(): bool { return $this->token_expires_at && $this->token_expires_at->isPast(); }
}
