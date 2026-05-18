<?php

namespace Database\Seeders;

use App\Enums\SkillType;
use App\Models\User;
use App\Models\UserSkill;
use App\Models\UserStats;
use Illuminate\Database\Seeder;

class UserWithStatsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()
            ->withSpanishNative()
            ->learningEnglish()
            ->create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
            ]);

        UserStats::updateOrCreate(
            ['user_id' => $user->id],
            [
                'xp' => 150,
                'streak_days' => 5,
                'last_activity_date' => now(),
            ]
        );

        foreach (SkillType::cases() as $skill) {
            UserSkill::create([
                'user_id' => $user->id,
                'skill' => $skill,
                'level' => rand(10, 40),
                'last_updated' => now(),
            ]);
        }
    }
}
