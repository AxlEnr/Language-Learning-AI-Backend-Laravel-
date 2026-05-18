<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class WordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'language_id' => Language::factory(),
            'word' => $this->faker->word(),
            'meaning' => $this->faker->sentence(),
            'example_sentence' => $this->faker->sentence(),
        ];
    }
}
