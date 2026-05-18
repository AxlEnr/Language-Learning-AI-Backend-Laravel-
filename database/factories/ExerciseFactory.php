<?php

namespace Database\Factories;

use App\Enums\ExerciseType;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(ExerciseType::cases());

        $metadata = match ($type) {
            ExerciseType::MULTIPLE_CHOICE => [
                'options' => $this->faker->words(4),
                'correct_option' => $this->faker->numberBetween(0, 3),
            ],
            ExerciseType::FILL_BLANK => [
                'sentence' => $this->faker->sentence(),
                'blank_word' => $this->faker->word(),
            ],
            ExerciseType::TRANSLATION => [
                'source_text' => $this->faker->sentence(),
                'target_language' => 'en',
            ],
            ExerciseType::SPEAKING => [
                'prompt_text' => $this->faker->sentence(),
                'expected_keywords' => $this->faker->words(3),
            ],
            ExerciseType::AI_CHAT => [
                'topic' => $this->faker->word(),
                'difficulty' => $this->faker->numberBetween(1, 5),
            ],
        };

        return [
            'lesson_id' => Lesson::factory(),
            'type' => $type,
            'prompt' => $this->faker->paragraph(),
            'metadata' => $metadata,
        ];
    }
}
