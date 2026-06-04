<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'pending_balance', 'currency', 'is_frozen'];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2', 'pending_balance' => 'decimal:2', 'is_frozen' => 'boolean'];
    }

    public function user()         { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class)->latest(); }

    public function credit(float $amount, string $description, array $metadata = []): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $metadata) {
            $this->increment('balance', $amount);
            $this->refresh();
            return $this->transactions()->create(['type' => 'credit', 'amount' => $amount, 'balance_after' => $this->balance, 'description' => $description, 'metadata' => $metadata]);
        });
    }

    public function debit(float $amount, string $description, array $metadata = []): WalletTransaction
    {
        if ($this->balance < $amount) throw new \RuntimeException('Insufficient wallet balance.');
        return DB::transaction(function () use ($amount, $description, $metadata) {
            $this->decrement('balance', $amount);
            $this->refresh();
            return $this->transactions()->create(['type' => 'debit', 'amount' => $amount, 'balance_after' => $this->balance, 'description' => $description, 'metadata' => $metadata]);
        });
    }

    public function hasSufficientBalance(float $amount): bool { return $this->balance >= $amount; }
}
