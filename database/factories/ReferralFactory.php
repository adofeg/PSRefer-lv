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
        $name = $this->faker->name();
        $email = $this->faker->safeEmail();
        $phone = $this->faker->phoneNumber();

        return [
            'associate_id' => \App\Models\Associate::factory(),
            'offering_id' => \App\Models\Offering::factory(),
            'status' => $this->faker->randomElement(['Pendiente', 'Contactado', 'Cierre', 'Cerrado', 'Perdido']),
            'metadata' => [
                'source' => 'factory',
                'client_name' => $name,
                'client_email' => $email,
                'client_phone' => $phone,
                'client_contact' => "{$email} / {$phone}",
            ],
            'notes' => $this->faker->sentence(),
            'revenue_generated' => 0,
        ];
    }
}
