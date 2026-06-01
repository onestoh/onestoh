<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AiChatSession extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['user_id', 'session_token', 'messages', 'message_count', 'tokens_used', 'escalated_to_human', 'last_activity_at'];
    protected $casts = ['messages' => 'array', 'escalated_to_human' => 'boolean', 'last_activity_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function addMessage(string $role, string $content): void {
        $msgs = $this->messages ?? [];
        $msgs[] = ['role' => $role, 'content' => $content, 'created_at' => now()->toISOString()];
        $this->update(['messages' => $msgs, 'message_count' => count($msgs), 'last_activity_at' => now()]);
    }
}
