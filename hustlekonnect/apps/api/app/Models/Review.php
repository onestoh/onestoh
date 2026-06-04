<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'booking_id','reviewer_id','reviewee_id','asset_id',
        'rating','comment','response','response_at',
        'is_public','review_type',
    ];

    protected $casts = [
        'rating'      => 'float',
        'is_public'   => 'boolean',
        'response_at' => 'datetime',
    ];

    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function reviewee(): BelongsTo { return $this->belongsTo(User::class, 'reviewee_id'); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
}
