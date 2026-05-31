<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReview extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
