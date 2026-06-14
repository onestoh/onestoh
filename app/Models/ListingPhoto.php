<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingPhoto extends Model
{
    protected $fillable = [
        'listing_id', 'file_path', 'is_primary', 'sort_order',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
