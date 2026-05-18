<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['code' => 'A1', 'description' => 'Beginner - Can understand and use familiar everyday expressions and very basic phrases.'],
            ['code' => 'A2', 'description' => 'Elementary - Can understand sentences and frequently used expressions related to areas of immediate relevance.'],
            ['code' => 'B1', 'description' => 'Intermediate - Can understand the main points of clear standard input on familiar matters.'],
            ['code' => 'B2', 'description' => 'Upper Intermediate - Can understand the main ideas of complex text on both concrete and abstract topics.'],
            ['code' => 'C1', 'description' => 'Advanced - Can understand a wide range of demanding, longer texts, and recognize implicit meaning.'],
            ['code' => 'C2', 'description' => 'Proficient - Can understand with ease virtually everything heard or read.'],
        ];

        foreach ($levels as $level) {
            Level::firstOrCreate(
                ['code' => $level['code']],
                ['description' => $level['description']]
            );
        }
    }
}
