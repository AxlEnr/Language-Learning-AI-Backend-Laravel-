<?php

namespace App\Services\Adaptive;

use App\Enums\ExerciseType;
use App\Enums\SkillType;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserSkill;
use App\Services\AI\AIService;
use Illuminate\Support\Collection;

class AdaptiveService
{
    public function __construct(
        protected AIService $aiService
    ) {
    }

    public function recommendNextLesson(User $user): ?Lesson
    {
        $weakestSkill = $this->getWeakestSkill($user);

        $targetLanguageId = $user->target_language_id ?? $user->native_language_id;

        if (!$targetLanguageId) {
            return null;
        }

        $modules = Module::where('language_id', $targetLanguageId)
            ->where('level_id', $user->level_id ?? 1)
            ->orderBy('order_index')
            ->get();

        foreach ($modules as $module) {
            $lesson = $this->findNextAppropriateLesson($user, $module, $weakestSkill);

            if ($lesson) {
                return $lesson;
            }
        }

        return null;
    }

    public function generateAIExercise(User $user, string $skill, string $type): array
    {
        $skillLevel = $user->getSkillLevel(SkillType::from($skill));
        $difficulty = max(1, min(5, (int) ceil($skillLevel / 20)));

        $context = [
            'language' => $this->getTargetLanguageName($user),
            'level' => $user->level?->code ?? 'A1',
            'skill' => $skill,
            'type' => $type,
            'difficulty' => $difficulty,
            'topic' => $this->getRecentTopic($user),
        ];

        return $this->aiService->generateAdaptiveExercise($context);
    }

    public function adjustDifficulty(User $user, Lesson $lesson): float
    {
        $recentAnswers = UserAnswer::where('user_id', $user->id)
            ->join('exercises', 'user_answers.exercise_id', '=', 'exercises.id')
            ->join('lessons', 'exercises.lesson_id', '=', 'lessons.id')
            ->where('lessons.module_id', $lesson->module_id)
            ->orderBy('user_answers.created_at', 'desc')
            ->limit(10)
            ->get();

        if ($recentAnswers->isEmpty()) {
            return 1.0;
        }

        $correctRate = $recentAnswers->where('is_correct', true)->count() / $recentAnswers->count();

        if ($correctRate >= 0.8) {
            return 1.2;
        }

        if ($correctRate <= 0.4) {
            return 0.8;
        }

        return 1.0;
    }

    public function updateSkillLevels(User $user): void
    {
        foreach (SkillType::cases() as $skill) {
            $this->updateSkillLevel($user, $skill);
        }
    }

    public function getWeakestSkill(User $user): SkillType
    {
        $skills = UserSkill::where('user_id', $user->id)
            ->orderBy('level')
            ->get();

        if ($skills->isEmpty()) {
            return SkillType::VOCABULARY;
        }

        return $skills->first()->skill;
    }

    public function getStrongestSkill(User $user): SkillType
    {
        $skills = UserSkill::where('user_id', $user->id)
            ->orderByDesc('level')
            ->get();

        if ($skills->isEmpty()) {
            return SkillType::VOCABULARY;
        }

        return $skills->first()->skill;
    }

    protected function findNextAppropriateLesson(User $user, Module $module, SkillType $skill): ?Lesson
    {
        $completedLessonIds = $user->lessonProgress()
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->toArray();

        $lesson = $module->lessons()
            ->whereNotIn('id', $completedLessonIds)
            ->where('type', $skill)
            ->orderBy('order_index')
            ->first();

        if ($lesson) {
            return $lesson;
        }

        return $module->lessons()
            ->whereNotIn('id', $completedLessonIds)
            ->orderBy('order_index')
            ->first();
    }

    protected function updateSkillLevel(User $user, SkillType $skill): void
    {
        $lessonAnswers = UserAnswer::where('user_id', $user->id)
            ->join('exercises', 'user_answers.exercise_id', '=', 'exercises.id')
            ->join('lessons', 'exercises.lesson_id', '=', 'lessons.id')
            ->where('lessons.type', $skill->value)
            ->get();

        if ($lessonAnswers->isEmpty()) {
            return;
        }

        $correctRate = $lessonAnswers->where('is_correct', true)->count() / $lessonAnswers->count();
        $totalAnswers = $lessonAnswers->count();

        $newLevel = min(100, (int) ($correctRate * 100));

        $userSkill = UserSkill::firstOrCreate(
            ['user_id' => $user->id, 'skill' => $skill],
            ['level' => 0, 'last_updated' => now()]
        );

        $currentLevel = $userSkill->level;
        $adjustment = $newLevel > $currentLevel ? 1 : -1;
        $magnitude = min(5, max(1, (int) ($totalAnswers / 10)));

        $userSkill->update([
            'level' => max(0, min(100, $currentLevel + ($adjustment * $magnitude))),
            'last_updated' => now(),
        ]);
    }

    protected function getTargetLanguageName(User $user): string
    {
        return $user->targetLanguage?->name ?? 'English';
    }

    protected function getRecentTopic(User $user): string
    {
        $recentLesson = $user->lessonProgress()
            ->where('status', 'in_progress')
            ->with('lesson.module')
            ->latest()
            ->first();

        return $recentLesson?->lesson?->module?->title ?? 'general';
    }
}
