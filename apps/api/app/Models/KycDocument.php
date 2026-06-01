<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDocument extends Model
{
    protected $fillable = ['user_id', 'document_type', 'file_path', 'file_name', 'mime_type', 'file_size', 'status', 'reviewed_by', 'rejection_reason', 'reviewed_at'];
    protected $casts = ['reviewed_at' => 'datetime'];
    protected $hidden = ['file_path'];
    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
