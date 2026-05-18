<?php

namespace App\Services\Vocabulary;

use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Support\Collection;

class VocabularyService
{
    public function addWordToReview(User $user, Word $word, int $initialFamiliarity = 0): UserWord
    {
        return UserWord::firstOrCreate(
            ['user_id' => $user->id, 'word_id' => $word->id],
            [
                'familiarity' => $initialFamiliarity,
                'next_review_at' => $this->calculateNextReview($initialFamiliarity),
            ]
        );
    }

    public function reviewWord(UserWord $userWord, bool $wasCorrect): UserWord
    {
        $familiarity = $userWord->familiarity;

        $familiarity = $wasCorrect
            ? min(5, $familiarity + 1)
            : max(0, $familiarity - 1);

        $userWord->update([
            'familiarity' => $familiarity,
            'next_review_at' => $this->calculateNextReview($familiarity),
            'last_reviewed_at' => now(),
        ]);

        return $userWord;
    }

    public function getWordsForReview(User $user): Collection
    {
        return UserWord::where('user_id', $user->id)
            ->where('next_review_at', '<=', now())
            ->where('familiarity', '<', 5)
            ->with(['word.language'])
            ->orderBy('next_review_at')
            ->get();
    }

    public function getMasteredWords(User $user): Collection
    {
        return UserWord::where('user_id', $user->id)
            ->where('familiarity', 5)
            ->with(['word.language'])
            ->orderBy('last_reviewed_at', 'desc')
            ->get();
    }

    public function getWordProgress(User $user): array
    {
        $totalWords = UserWord::where('user_id', $user->id)->count();
        $masteredWords = UserWord::where('user_id', $user->id)->where('familiarity', 5)->count();
        $dueForReview = UserWord::where('user_id', $user->id)
            ->where('next_review_at', '<=', now())
            ->count();

        return [
            'total' => $totalWords,
            'mastered' => $masteredWords,
            'due_for_review' => $dueForReview,
            'progress_percentage' => $totalWords > 0 ? round(($masteredWords / $totalWords) * 100, 2) : 0,
        ];
    }

    protected function calculateNextReview(int $familiarity): string
    {
        $intervals = [
            0 => '1 hour',
            1 => '1 day',
            2 => '3 days',
            3 => '7 days',
            4 => '14 days',
            5 => '30 days',
        ];

        $interval = $intervals[$familiarity] ?? '1 day';

        return now()->add($interval)->toDateTimeString();
    }
}
