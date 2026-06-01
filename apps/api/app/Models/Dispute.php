<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = ['booking_id', 'raised_by', 'type', 'description', 'evidence', 'respondent_evidence', 'status', 'admin_id', 'ruling', 'escrow_split', 'appeal_used', 'appeal_at', 'resolved_at'];
    protected $casts = ['evidence' => 'array', 'respondent_evidence' => 'array', 'escrow_split' => 'array', 'appeal_used' => 'boolean', 'appeal_at' => 'datetime', 'resolved_at' => 'datetime'];
    public function booking() { return $this->belongsTo(Booking::class); }
    public function raisedBy() { return $this->belongsTo(User::class, 'raised_by'); }
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
}
