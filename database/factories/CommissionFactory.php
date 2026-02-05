<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referral_id' => \App\Models\Referral::factory(),
            'user_id' => \App\Models\User::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'status' => $this->faker->randomElement(['Pending', 'Paid', 'Cancelled']),
            'commission_percentage' => 10.00,
            'paid_at' => null,
        ];
    }
}
