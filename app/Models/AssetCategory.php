<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'description', 'type',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class, 'category_id');
    }
}
