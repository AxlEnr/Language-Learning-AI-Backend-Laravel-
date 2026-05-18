<?php

namespace App\Traits;

use App\Models\UserStats;

trait HasUserStats
{
    public function initializeHasUserStats(): void
    {
        $this->ensureStatsExist();
    }

    public function ensureStatsExist(): UserStats
    {
        return $this->stats()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'xp' => 0,
            'streak_days' => 0,
            'last_activity_date' => now(),
        ]);
    }

    public function addXp(int $amount): void
    {
        $this->ensureStatsExist()->addXp($amount);
    }

    public function updateActivityStreak(): void
    {
        $this->ensureStatsExist()->updateStreak();
    }
}
