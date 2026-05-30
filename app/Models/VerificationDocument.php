<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationDocument extends Model
{
    protected $guarded = [];

    public function verification()
    {
        return $this->belongsTo(Verification::class);
    }
}
