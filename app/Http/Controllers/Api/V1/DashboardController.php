<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserLessonProgress;
use App\Services\Adaptive\AdaptiveService;
use App\Services\Vocabulary\VocabularyService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected AdaptiveService $adaptiveService,
        protected VocabularyService $vocabularyService
    ) {
    }

    public function index(): JsonResponse
    {
        $user = request()->user();
        $stats = $user->ensureStatsExist();

        $progress = UserLessonProgress::where('user_id', $user->id)
            ->with(['lesson.module'])
            ->get();

        $currentLesson = $progress->where('status', 'in_progress')->first();
        $completedCount = $progress->where('status', 'completed')->count();
        $wordsForReview = $this->vocabularyService->getWordProgress($user);

        $recommendedLesson = $this->adaptiveService->recommendNextLesson($user);

        return response()->json([
            'dashboard' => [
                'stats' => [
                    'xp' => $stats->xp,
                    'streak_days' => $stats->streak_days,
                    'level' => $user->level?->code ?? 'A1',
                ],
                'current_lesson' => $currentLesson ? [
                    'id' => $currentLesson->lesson->id,
                    'title' => $currentLesson->lesson->title,
                    'module' => $currentLesson->lesson->module->title,
                    'progress_score' => $currentLesson->score,
                ] : null,
                'lessons_completed' => $completedCount,
                'vocabulary' => [
                    'words_due' => $wordsForReview['due_for_review'],
                    'mastered' => $wordsForReview['mastered'],
                ],
                'recommended_next' => $recommendedLesson ? [
                    'id' => $recommendedLesson->id,
                    'title' => $recommendedLesson->title,
                    'type' => $recommendedLesson->type->value,
                ] : null,
                'skills' => $user->skills->map(fn ($skill) => [
                    'name' => $skill->skill->value,
                    'level' => $skill->level,
                ]),
            ],
        ]);
    }
}
