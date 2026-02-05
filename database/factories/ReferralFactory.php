<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_name' => $this->faker->name(),
            'client_contact' => $this->faker->phoneNumber(),
            'user_id' => \App\Models\User::factory(),
            'offering_id' => \App\Models\Offering::factory(),
            'status' => $this->faker->randomElement(['Pendiente', 'Contactado', 'Cierre', 'Cerrado', 'Perdido']),
            'metadata' => ['source' => 'factory'],
            'notes' => $this->faker->sentence(),
            'revenue_generated' => 0,
        ];
    }
}
