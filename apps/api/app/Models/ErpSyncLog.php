<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ErpSyncLog extends Model
{
    protected $fillable = ['erp_integration_id', 'direction', 'entity_type', 'local_id', 'remote_id', 'status', 'error_message'];
    public function integration() { return $this->belongsTo(ErpIntegration::class, 'erp_integration_id'); }
}
