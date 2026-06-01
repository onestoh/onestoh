<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    protected $fillable = [
        'insurance_policy_id', 'filed_by', 'description', 'claimed_amount',
        'approved_amount', 'status', 'evidence', 'insurer_notes', 'resolved_at',
    ];

    protected $casts = [
        'claimed_amount'  => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'evidence'        => 'array',
        'resolved_at'     => 'datetime',
    ];

    public function policy()   { return $this->belongsTo(InsurancePolicy::class, 'insurance_policy_id'); }
    public function filedBy()  { return $this->belongsTo(User::class, 'filed_by'); }
}
