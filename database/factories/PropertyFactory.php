<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'        => $this->faker->sentence(4),
            'slug'         => $this->faker->unique()->slug(),
            'description'  => $this->faker->paragraphs(2, true),
            'type'         => $this->faker->randomElement(['house', 'apartment', 'land', 'commercial']),
            'listing_type' => $this->faker->randomElement(['sale', 'rent', 'airbnb']),
            'status'       => 'active',
            'price'        => $this->faker->numberBetween(5000000, 50000000),
            'county'       => 'Nairobi',
            'location'     => $this->faker->city(),
            'is_featured'  => false,
            'view_count'   => 0,
            'save_count'   => 0,
        ];
    }
}
