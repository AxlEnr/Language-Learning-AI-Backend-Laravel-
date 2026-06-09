<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            LevelSeeder::class,
            WordSeeder::class,
            EnglishVocabularySeeder::class,
            DemoContentSeeder::class,
            EnglishLessonsSeeder::class,
            AdminUserSeeder::class,
            // UserWithStatsSeeder::class,
        ]);
    }
}
