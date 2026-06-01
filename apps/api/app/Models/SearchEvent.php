<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SearchEvent extends Model
{
    protected $fillable = ['user_id', 'query', 'category', 'county', 'duration_type', 'price_min', 'price_max', 'results_count', 'resulted_in_booking', 'session_id'];
    protected $casts = ['resulted_in_booking' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
