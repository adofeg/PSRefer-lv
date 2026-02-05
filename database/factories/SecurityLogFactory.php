<?php

namespace Database\Factories;

use App\Models\SecurityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SecurityLog>
 */
class SecurityLogFactory extends Factory
{
    protected $model = SecurityLog::class;

    public function definition(): array
    {
        return [
            'event_type' => $this->faker->randomElement(['login', 'logout', 'password_reset']),
            'email' => $this->faker->optional()->safeEmail(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'metadata' => $this->faker->optional()->randomElement([['status' => 'ok'], ['reason' => 'invalid_password']]),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}