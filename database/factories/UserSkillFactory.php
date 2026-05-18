<?php

namespace Database\Factories;

use App\Enums\SkillType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'skill' => $this->faker->randomElement(SkillType::cases()),
            'level' => $this->faker->numberBetween(0, 100),
            'last_updated' => now(),
        ];
    }
}
