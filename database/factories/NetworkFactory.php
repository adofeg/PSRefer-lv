<?php

namespace Database\Factories;

use App\Models\Associate;
use App\Models\Network;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Network>
 */
class NetworkFactory extends Factory
{
    protected $model = Network::class;

    public function definition(): array
    {
        $parent = Associate::factory();
        $child = Associate::factory();

        return [
            'parent_associate_id' => $parent,
            'child_associate_id' => $child,
            'level' => 1,
            'total_sales' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}