<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApiWebhookDelivery extends Model
{
    protected $fillable = ['api_client_id', 'event_type', 'payload', 'signature', 'attempt_count', 'response_status', 'response_body', 'status', 'delivered_at', 'next_retry_at'];
    protected $casts = ['payload' => 'array', 'delivered_at' => 'datetime', 'next_retry_at' => 'datetime'];
    public function apiClient() { return $this->belongsTo(ApiClient::class); }
}
