<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperProject extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'completion_date' => 'date',
        ];
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
