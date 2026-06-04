<?php
namespace Database\Factories;

use App\Models\Yard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class YardFactory extends Factory
{
    protected $model = Yard::class;

    public function definition(): array
    {
        $name = fake()->company() . ' Yard';
        return [
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . fake()->randomNumber(4),
            'description' => fake()->paragraph(),
            'country'     => fake()->randomElement(['KE', 'UG', 'TZ', 'NG', 'GH', 'ZA']),
            'city'        => fake()->city(),
            'address'     => fake()->address(),
            'latitude'    => fake()->latitude(-10, 10),
            'longitude'   => fake()->longitude(28, 42),
            'yard_type'   => fake()->randomElement(['vehicle', 'machinery', 'mixed']),
            'is_active'   => true,
            'is_verified' => true,
            'rating'      => fake()->randomFloat(1, 3.5, 5.0),
        ];
    }
}
