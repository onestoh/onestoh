<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Yard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','name','slug','description','country','city','address',
        'latitude','longitude','yard_type','is_active','is_verified',
        'phone','email','rating','total_reviews','total_assets',
        'operating_hours','amenities','images',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'amenities'       => 'array',
        'images'          => 'array',
        'latitude'        => 'float',
        'longitude'       => 'float',
        'is_active'       => 'boolean',
        'is_verified'     => 'boolean',
        'rating'          => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
