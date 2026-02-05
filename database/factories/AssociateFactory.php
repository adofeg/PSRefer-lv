<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Associate>
 */
class AssociateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'balance' => $this->faker->randomFloat(2, 0, 5000),
            'category' => $this->faker->randomElement(['Marketing', 'Sales', 'Realtor', 'General']),
            'payment_info' => ['details' => $this->faker->sentence()],
            'w9_status' => 'pending',
        ];
    }
}
