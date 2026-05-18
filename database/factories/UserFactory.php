<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'native_language_id' => fn () => Language::query()->inRandomOrder()->value('id') ?? Language::factory()->create()->id,
            'target_language_id' => fn () => Language::query()->inRandomOrder()->value('id') ?? Language::factory()->create()->id,
            'level_id' => fn () => Level::query()->inRandomOrder()->value('id') ?? Level::factory()->create()->id,
        ];
    }

    public function withSpanishNative(): static
    {
        return $this->state(fn (array $attributes) => [
            'native_language_id' => Language::where('code', 'es')->first()?->id ?? Language::factory()->create(['code' => 'es', 'name' => 'Spanish'])->id,
        ]);
    }

    public function learningEnglish(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_language_id' => Language::where('code', 'en')->first()?->id ?? Language::factory()->create(['code' => 'en', 'name' => 'English'])->id,
        ]);
    }
}
