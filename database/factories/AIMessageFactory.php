<?php

namespace Database\Factories;

use App\Enums\AIRole;
use App\Models\AIConversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => AIConversation::factory(),
            'role' => $this->faker->randomElement(AIRole::cases()),
            'message' => $this->faker->paragraph(),
            'metadata' => [
                'tokens_used' => $this->faker->numberBetween(10, 500),
                'model' => 'gpt-4o-mini',
            ],
        ];
    }
}
