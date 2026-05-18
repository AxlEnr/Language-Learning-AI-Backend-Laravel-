<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'context' => [
                'topic' => $this->faker->word(),
                'language' => 'en',
                'difficulty' => $this->faker->numberBetween(1, 5),
            ],
        ];
    }
}
