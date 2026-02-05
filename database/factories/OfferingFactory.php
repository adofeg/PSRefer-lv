<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offering>
 */
class OfferingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->bs(),
            'description' => $this->faker->paragraph(),
            'base_price' => $this->faker->randomFloat(2, 1000, 50000),
            'commission_rate' => $this->faker->randomFloat(2, 5, 20),
            'is_active' => true,
            'form_schema' => [],
            'commission_config' => [],
            'owner_employee_id' => \App\Models\Employee::factory(),
        ];
    }
}
