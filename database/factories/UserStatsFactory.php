<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserStatsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'xp' => $this->faker->numberBetween(0, 10000),
            'streak_days' => $this->faker->numberBetween(0, 365),
            'last_activity_date' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}
