<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'               => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'phone'              => '+254' . fake()->numerify('#########'),
            'password'           => Hash::make('Secret@123'),
            'role'               => fake()->randomElement(['client', 'owner', 'broker']),
            'kyc_status'         => 'approved',
            'country'            => fake()->randomElement(['KE', 'UG', 'TZ', 'NG', 'GH', 'ZA']),
            'preferred_currency' => 'KES',
            'trust_score'        => fake()->numberBetween(60, 100),
            'referral_code'      => strtoupper(Str::random(8)),
            'is_active'          => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attrs) => ['email_verified_at' => null]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attrs) => ['is_active' => false]);
    }

    public function owner(): static
    {
        return $this->state(fn(array $attrs) => ['role' => 'owner']);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attrs) => ['role' => 'admin']);
    }
}
