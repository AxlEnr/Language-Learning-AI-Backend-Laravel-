<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'language_id' => Language::factory(),
            'level_id' => Level::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'order_index' => $this->faker->numberBetween(1, 100),
        ];
    }
}
