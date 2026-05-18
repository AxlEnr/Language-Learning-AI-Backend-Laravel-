<?php

namespace Database\Factories;

use App\Enums\LessonType;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'title' => $this->faker->sentence(3),
            'type' => $this->faker->randomElement(LessonType::cases()),
            'order_index' => $this->faker->numberBetween(1, 50),
        ];
    }
}
