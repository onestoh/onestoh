<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'booking_id', 'reviewer_id', 'reviewee_id', 'reviewer_type',
        'overall_rating', 'sub_ratings', 'comment', 'tags', 'photos',
        'owner_response', 'owner_responded_at', 'is_published', 'admin_flagged', 'flag_reason',
    ];
    protected $casts = [
        'sub_ratings' => 'array',
        'tags' => 'array',
        'photos' => 'array',
        'is_published' => 'boolean',
        'admin_flagged' => 'boolean',
        'owner_responded_at' => 'datetime',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function reviewee() { return $this->belongsTo(User::class, 'reviewee_id'); }
}
