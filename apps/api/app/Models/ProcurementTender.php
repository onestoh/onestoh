<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProcurementTender extends Model
{
    protected $fillable = ['government_entity_id', 'tender_number', 'title', 'description', 'asset_category', 'quantity_required', 'duration_type', 'duration_value', 'budget_per_unit', 'total_budget', 'submission_deadline', 'service_start_date', 'service_end_date', 'status', 'requirements'];
    protected $casts = ['submission_deadline' => 'date', 'service_start_date' => 'date', 'service_end_date' => 'date', 'requirements' => 'array', 'budget_per_unit' => 'decimal:2', 'total_budget' => 'decimal:2'];
    public function entity() { return $this->belongsTo(GovernmentEntity::class, 'government_entity_id'); }
    public function bids() { return $this->hasMany(TenderBid::class, 'tender_id'); }
    public function isOpen(): bool { return $this->status === 'open' && $this->submission_deadline->isFuture(); }
}
