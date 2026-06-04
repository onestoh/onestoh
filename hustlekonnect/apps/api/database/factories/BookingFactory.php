<?php
namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+30 days');
        $end   = fake()->dateTimeBetween($start, '+60 days');
        $days  = (new \DateTime($start->format('Y-m-d')))->diff(new \DateTime($end->format('Y-m-d')))->days ?: 1;
        $base  = $days * fake()->randomFloat(0, 2000, 20000);

        return [
            'id'              => (string) Str::uuid(),
            'status'          => fake()->randomElement(['pending_payment', 'confirmed', 'active', 'completed']),
            'start_date'      => $start,
            'end_date'        => $end,
            'rental_days'     => $days,
            'currency'        => 'KES',
            'base_amount_kes' => $base,
            'platform_fee_kes'=> $base * 0.05,
            'driver_fee_kes'  => 0,
            'total_amount_kes'=> $base * 1.05,
            'with_driver'     => false,
            'insurance_type'  => 'none',
        ];
    }
}
