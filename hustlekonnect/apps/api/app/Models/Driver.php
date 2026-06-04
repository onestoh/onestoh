<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    protected $fillable = [
        'user_id','yard_id','name','phone','license_number','license_expiry',
        'license_classes','photo','is_available','is_verified',
        'rating','total_trips','country',
    ];

    protected $casts = [
        'license_classes' => 'array',
        'license_expiry'  => 'date',
        'is_available'    => 'boolean',
        'is_verified'     => 'boolean',
        'rating'          => 'float',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function yard(): BelongsTo { return $this->belongsTo(Yard::class); }
}
