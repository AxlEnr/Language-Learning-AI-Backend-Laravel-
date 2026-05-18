<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'exercise_id' => Exercise::factory(),
            'answer' => $this->faker->sentence(),
            'is_correct' => $this->faker->boolean(),
            'feedback' => $this->faker->sentence(),
        ];
    }
}
