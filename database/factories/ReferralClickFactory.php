<?php

namespace Database\Factories;

use App\Models\Associate;
use App\Models\Offering;
use App\Models\ReferralClick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReferralClick>
 */
class ReferralClickFactory extends Factory
{
    protected $model = ReferralClick::class;

    public function definition(): array
    {
        return [
            'referrer_associate_id' => Associate::factory(),
            'offering_id' => Offering::factory(),
            'link_type' => $this->faker->randomElement(['general', 'specific', 'conversion']),
            'user_agent' => $this->faker->userAgent(),
            'ip_address' => $this->faker->ipv4(),
            'clicked_at' => $this->faker->dateTime(),
        ];
    }
}