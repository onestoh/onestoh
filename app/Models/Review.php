<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'booking_id', 'reviewer_id', 'reviewee_id', 'listing_id',
        'overall_rating', 'condition_rating', 'punctuality_rating',
        'value_rating', 'driver_rating', 'comment', 'would_rent_again',
        'tags', 'is_public', 'admin_flagged', 'owner_response',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'admin_flagged' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
