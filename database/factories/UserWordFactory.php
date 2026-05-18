<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserWordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'word_id' => Word::factory(),
            'familiarity' => $this->faker->numberBetween(0, 5),
            'next_review_at' => now()->addDays($this->faker->numberBetween(1, 30)),
            'last_reviewed_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}
