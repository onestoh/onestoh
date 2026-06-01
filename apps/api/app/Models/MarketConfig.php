<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MarketConfig extends Model
{
    protected $fillable = ['country_code', 'country_name', 'currency_code', 'currency_symbol', 'timezone', 'phone_prefix', 'phone_format', 'payment_gateways', 'id_document_types', 'language_code', 'is_launched', 'is_beta', 'launch_date', 'regulatory_notes'];
    protected $casts = ['payment_gateways' => 'array', 'id_document_types' => 'array', 'regulatory_notes' => 'array', 'is_launched' => 'boolean', 'is_beta' => 'boolean', 'launch_date' => 'date'];
}
