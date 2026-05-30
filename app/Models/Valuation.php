<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valuation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function valuer()
    {
        return $this->belongsTo(User::class, 'valuer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
