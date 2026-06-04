<?php
namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'balance'   => fake()->randomFloat(2, 0, 500000),
            'currency'  => 'KES',
            'is_frozen' => false,
        ];
    }
}
