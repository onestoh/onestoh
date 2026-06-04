<?php
namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $makes  = ['Toyota', 'Isuzu', 'Mitsubishi', 'CAT', 'Komatsu', 'Volvo', 'Mercedes', 'MAN'];
        $types  = ['car', 'truck', 'bus', 'excavator', 'crane', 'loader', 'grader', 'trailer'];
        $make   = fake()->randomElement($makes);
        $type   = fake()->randomElement($types);

        return [
            'title'           => "{$make} {$type} " . fake()->year(),
            'description'     => fake()->paragraph(),
            'asset_type'      => $type,
            'make'            => $make,
            'model'           => fake()->bothify('??-###'),
            'year'            => fake()->year(),
            'daily_rate'      => fake()->randomFloat(0, 2000, 50000),
            'driver_daily_rate'=> fake()->randomFloat(0, 500, 3000),
            'currency'        => 'KES',
            'status'          => 'available',
            'is_listed'       => true,
            'capacity'        => fake()->numberBetween(1, 50),
            'mileage'         => fake()->numberBetween(0, 200000),
            'plate_number'    => strtoupper(fake()->bothify('K??-###?')),
            'vin'             => strtoupper(fake()->bothify('?????????????????')),
        ];
    }
}
