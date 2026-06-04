<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycDocument extends Model
{
    protected $fillable = [
        'user_id','document_type','document_number','country',
        'file_path','status','rejection_reason','verified_at','verified_by',
        'provider','provider_reference','provider_response',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'verified_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
}
