<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformNotification extends Model
{
    use HasUuids;

    protected $fillable = [
        'id','user_id','title','body','type','data',
        'read_at','channel','is_sent',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function markRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}
