<?php

namespace App\Services\Progression;

use App\Enums\ExerciseType;
use App\Enums\ProgressStatus;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserLessonProgress;

class ProgressionService
{
    public function startLesson(User $user, Lesson $lesson): UserLessonProgress
    {
        $progress = UserLessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => ProgressStatus::IN_PROGRESS, 'score' => 0]
        );

        return $progress;
    }

    public function submitAnswer(User $user, Exercise $exercise, string $answer): UserAnswer
    {
        $userAnswer = UserAnswer::create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'answer' => $answer,
            'is_correct' => null,
            'feedback' => null,
        ]);

        $this->evaluateAnswer($userAnswer, $exercise, $answer);

        return $userAnswer;
    }

    public function completeLesson(User $user, Lesson $lesson, float $score): UserLessonProgress
    {
        $progress = UserLessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => ProgressStatus::IN_PROGRESS, 'score' => 0]
        );

        $progress->update([
            'status' => ProgressStatus::COMPLETED,
            'score' => $score,
            'completed_at' => now(),
        ]);

        $xpEarned = $this->calculateXp($score, $lesson);
        $user->addXp($xpEarned);
        $user->updateActivityStreak();

        $this->unlockNextLesson($lesson, $user);

        return $progress;
    }

    public function getLessonProgress(User $user, Lesson $lesson): ?UserLessonProgress
    {
        return UserLessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();
    }

    public function calculateLessonScore(User $user, Lesson $lesson): float
    {
        $exerciseIds = $lesson->exercises()->pluck('id');

        if ($exerciseIds->isEmpty()) {
            return 0;
        }

        $totalAnswers = UserAnswer::where('user_id', $user->id)
            ->whereIn('exercise_id', $exerciseIds)
            ->count();

        if ($totalAnswers === 0) {
            return 0;
        }

        $correctAnswers = UserAnswer::where('user_id', $user->id)
            ->whereIn('exercise_id', $exerciseIds)
            ->where('is_correct', true)
            ->count();

        return round(($correctAnswers / $totalAnswers) * 100, 2);
    }

    protected function evaluateAnswer(UserAnswer $userAnswer, Exercise $exercise, string $answer): void
    {
        $metadata = $exercise->metadata ?? [];

        $result = match ($exercise->type) {
            ExerciseType::MULTIPLE_CHOICE => $this->evaluateMultipleChoice($answer, $metadata),
            ExerciseType::FILL_BLANK => $this->evaluateFillBlank($answer, $metadata),
            ExerciseType::TRANSLATION => $this->evaluateTranslation($answer, $metadata),
            default => ['is_correct' => null, 'feedback' => 'This exercise requires AI evaluation.'],
        };

        $userAnswer->update([
            'is_correct' => $result['is_correct'],
            'feedback' => $result['feedback'],
        ]);
    }

    protected function evaluateMultipleChoice(string $answer, array $metadata): array
    {
        $correctOption = $metadata['correct_option'] ?? null;

        if ($correctOption === null) {
            return ['is_correct' => null, 'feedback' => 'Exercise not properly configured.'];
        }

        $isCorrect = (int) $answer === $correctOption;

        return [
            'is_correct' => $isCorrect,
            'feedback' => $isCorrect ? 'Correct!' : 'Incorrect. The correct answer is option ' . ($correctOption + 1) . '.',
        ];
    }

    protected function evaluateFillBlank(string $answer, array $metadata): array
    {
        $correctWord = strtolower(trim($metadata['blank_word'] ?? ''));
        $userAnswer = strtolower(trim($answer));

        $isCorrect = $userAnswer === $correctWord;

        return [
            'is_correct' => $isCorrect,
            'feedback' => $isCorrect
                ? 'Correct!'
                : "The correct answer is: {$correctWord}",
        ];
    }

    protected function evaluateTranslation(string $answer, array $metadata): array
    {
        $acceptedAnswers = array_map('strtolower', $metadata['accepted_answers'] ?? []);
        $userAnswer = strtolower(trim($answer));

        $isCorrect = in_array($userAnswer, $acceptedAnswers, true);
        $correctAnswer = $acceptedAnswers[0] ?? '';

        return [
            'is_correct' => $isCorrect,
            'feedback' => $isCorrect
                ? 'Correct translation!'
                : "The correct translation is: {$correctAnswer}",
        ];
    }

    protected function calculateXp(float $score, Lesson $lesson): int
    {
        $baseXp = 10;
        $scoreMultiplier = $score / 100;

        return (int) round($baseXp + ($baseXp * $scoreMultiplier));
    }

    protected function unlockNextLesson(Lesson $completedLesson, User $user): void
    {
        $nextLesson = Lesson::where('module_id', $completedLesson->module_id)
            ->where('order_index', '>', $completedLesson->order_index)
            ->orderBy('order_index')
            ->first();

        if ($nextLesson) {
            UserLessonProgress::firstOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $nextLesson->id],
                ['status' => ProgressStatus::IN_PROGRESS, 'score' => 0]
            );
        }
    }
}
