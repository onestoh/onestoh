<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'asset_id', 'name', 'interval_type', 'interval_value',
        'last_done_at', 'next_due_at', 'alert_days_before', 'is_active',
    ];

    protected $casts = [
        'last_done_at' => 'date',
        'next_due_at'  => 'date',
        'is_active'    => 'boolean',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }

    public function isDueSoon(): bool
    {
        return $this->next_due_at && $this->next_due_at->diffInDays(now()) <= $this->alert_days_before;
    }
}
