<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GovernmentEntity extends Model
{
    protected $fillable = ['name', 'registration_number', 'county', 'ministry_department', 'tier', 'procurement_email', 'po_box', 'is_verified', 'is_active'];
    protected $casts = ['is_verified' => 'boolean', 'is_active' => 'boolean'];
    public function tenders() { return $this->hasMany(ProcurementTender::class); }
}
