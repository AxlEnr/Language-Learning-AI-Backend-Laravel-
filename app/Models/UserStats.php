<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'xp',
        'streak_days',
        'last_activity_date',
    ];

    protected function casts(): array
    {
        return [
            'xp' => 'integer',
            'streak_days' => 'integer',
            'last_activity_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addXp(int $amount): void
    {
        $this->increment('xp', $amount);
    }

    public function updateStreak(): void
    {
        $today = now()->toDateString();

        if ($this->last_activity_date?->toDateString() === $today) {
            return;
        }

        $yesterday = now()->subDay()->toDateString();

        if ($this->last_activity_date?->toDateString() === $yesterday) {
            $this->increment('streak_days');
        } else {
            $this->update([
                'streak_days' => 1,
                'last_activity_date' => $today,
            ]);
            return;
        }

        $this->update(['last_activity_date' => $today]);
    }
}
