<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TenantAdmin extends Model
{
    protected $fillable = ['tenant_id', 'user_id', 'role'];
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
}
