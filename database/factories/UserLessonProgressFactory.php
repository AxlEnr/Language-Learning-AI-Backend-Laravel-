<?php

namespace Database\Factories;

use App\Enums\ProgressStatus;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserLessonProgressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'status' => $this->faker->randomElement(ProgressStatus::cases()),
            'score' => $this->faker->randomFloat(2, 0, 100),
            'completed_at' => $this->faker->dateTime(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProgressStatus::COMPLETED,
            'score' => $this->faker->randomFloat(2, 70, 100),
            'completed_at' => now(),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProgressStatus::IN_PROGRESS,
            'score' => $this->faker->randomFloat(2, 0, 69),
            'completed_at' => null,
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProgressStatus::LOCKED,
            'score' => 0,
            'completed_at' => null,
        ]);
    }
}
