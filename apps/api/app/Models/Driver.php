<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = ['user_id', 'primary_yard_id', 'licence_class', 'licence_number', 'licence_expiry', 'is_freelance', 'is_available', 'average_rating', 'total_trips', 'incident_free_trips'];
    protected $casts = ['licence_expiry' => 'date', 'is_freelance' => 'boolean', 'is_available' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function yard() { return $this->belongsTo(Yard::class, 'primary_yard_id'); }
}
