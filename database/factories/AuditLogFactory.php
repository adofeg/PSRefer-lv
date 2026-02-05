<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'entity' => $this->faker->word(),
            'entity_id' => $this->faker->numberBetween(1, 10000),
            'action' => $this->faker->randomElement(['CREATE', 'UPDATE', 'DELETE']),
            'event_type' => $this->faker->optional()->slug(),
            'previous_data' => $this->faker->optional()->randomElement([['foo' => 'bar'], ['status' => 'old']]),
            'new_data' => $this->faker->optional()->randomElement([['foo' => 'baz'], ['status' => 'new']]),
            'description' => $this->faker->optional()->sentence(),
            'metadata' => $this->faker->optional()->randomElement([['ip' => $this->faker->ipv4()], ['ua' => $this->faker->userAgent()]]),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}