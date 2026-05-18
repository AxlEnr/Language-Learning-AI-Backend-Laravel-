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
            DemoContentSeeder::class,
            UserWithStatsSeeder::class,
        ]);
    }
}
