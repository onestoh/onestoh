<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'booking_id', 'from_user_id', 'to_user_id', 'message',
        'file_path', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
