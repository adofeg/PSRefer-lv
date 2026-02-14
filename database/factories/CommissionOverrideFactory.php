<?php

namespace Database\Factories;

use App\Models\Associate;
use App\Models\CommissionOverride;
use App\Models\Offering;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommissionOverride>
 */
class CommissionOverrideFactory extends Factory
{
    protected $model = CommissionOverride::class;

    public function definition(): array
    {
        return [
            'associate_id' => Associate::factory(),
            'offering_id' => Offering::factory(),
            'commission_rate' => $this->faker->randomFloat(2, 1, 50),
            'is_active' => true,
        ];
    }
}
