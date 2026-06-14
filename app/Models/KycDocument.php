<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDocument extends Model
{
    protected $fillable = [
        'user_id', 'document_type', 'file_path', 'status', 'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
