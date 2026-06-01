<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TenderBid extends Model
{
    protected $fillable = ['tender_id', 'yard_id', 'owner_id', 'offered_assets', 'total_bid_value', 'rate_per_unit', 'proposal_notes', 'compliance_documents', 'status', 'rejection_reason', 'awarded_at'];
    protected $casts = ['offered_assets' => 'array', 'compliance_documents' => 'array', 'awarded_at' => 'datetime', 'total_bid_value' => 'decimal:2', 'rate_per_unit' => 'decimal:2'];
    public function tender() { return $this->belongsTo(ProcurementTender::class); }
    public function yard() { return $this->belongsTo(Yard::class); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
}
