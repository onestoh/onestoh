<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'pending_balance', 'deposit_balance', 'currency'];
    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'deposit_balance' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class)->latest(); }

    public function credit(float $amount, string $type, string $refId, string $desc): WalletTransaction
    {
        $this->increment('balance', $amount);
        return $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'reference_type' => $type,
            'reference_id' => $refId,
            'description' => $desc,
        ]);
    }

    public function debit(float $amount, string $type, string $refId, string $desc): WalletTransaction
    {
        $this->decrement('balance', $amount);
        return $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'reference_type' => $type,
            'reference_id' => $refId,
            'description' => $desc,
        ]);
    }
}
