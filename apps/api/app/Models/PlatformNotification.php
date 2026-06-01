<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PlatformNotification extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'type', 'title', 'body', 'data', 'channels_sent', 'read_at'];
    protected $casts = ['data' => 'array', 'channels_sent' => 'array', 'read_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
