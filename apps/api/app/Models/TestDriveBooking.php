<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TestDriveBooking extends Model
{
    protected $fillable = [
        'asset_id', 'client_id', 'scheduled_at', 'duration_minutes',
        'status', 'deposit_amount', 'deposit_returned', 'inspection_notes',
        'inspection_photos',
    ];

    protected $casts = [
        'scheduled_at'      => 'datetime',
        'deposit_amount'    => 'decimal:2',
        'deposit_returned'  => 'boolean',
        'inspection_photos' => 'array',
    ];

    public function asset()  { return $this->belongsTo(Asset::class); }
    public function client() { return $this->belongsTo(User::class, 'client_id'); }
}
