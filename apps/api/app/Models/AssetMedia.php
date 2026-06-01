<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMedia extends Model
{
    protected $fillable = ['asset_id', 'type', 'file_path', 'thumbnail_path', 'file_hash', 'sort_order', 'is_primary', 'file_size'];
    protected $casts = ['is_primary' => 'boolean'];
    public function asset() { return $this->belongsTo(Asset::class); }
}
