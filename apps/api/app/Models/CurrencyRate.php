<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = ['base_currency', 'target_currency', 'rate', 'source', 'fetched_at'];
    protected $casts = ['rate' => 'decimal:8', 'fetched_at' => 'datetime'];

    public static function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;
        $rate = static::where('base_currency', $from)->where('target_currency', $to)->latest('fetched_at')->value('rate');
        return $rate ? round($amount * $rate, 2) : $amount;
    }
}
