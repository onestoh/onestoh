<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'logged_by', 'service_type', 'description', 'service_centre',
        'cost', 'odometer_at_service', 'next_service_km', 'next_service_date',
        'service_date', 'attachments',
    ];

    protected $casts = [
        'service_date'      => 'date',
        'next_service_date' => 'date',
        'cost'              => 'decimal:2',
        'attachments'       => 'array',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function loggedBy() { return $this->belongsTo(User::class, 'logged_by'); }
}
