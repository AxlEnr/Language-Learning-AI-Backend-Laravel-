<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    public function definition(): array
    {
        $levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

        return [
            'code' => $this->faker->randomElement($levels),
            'description' => $this->faker->sentence(),
        ];
    }
}
