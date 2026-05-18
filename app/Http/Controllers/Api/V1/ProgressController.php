<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Progress\CompleteLessonRequest;
use App\Http\Requests\Progress\StartLessonRequest;
use App\Http\Requests\Progress\SubmitAnswerRequest;
use App\Models\Lesson;
use App\Models\UserLessonProgress;
use App\Services\Adaptive\AdaptiveService;
use App\Services\Progression\ProgressionService;
use Illuminate\Http\JsonResponse;

class ProgressController extends Controller
{
    public function __construct(
        protected ProgressionService $progressionService,
        protected AdaptiveService $adaptiveService
    ) {
    }

    public function startLesson(StartLessonRequest $request): JsonResponse
    {
        $lesson = Lesson::findOrFail($request->lesson_id);
        $progress = $this->progressionService->startLesson($request->user(), $lesson);

        return response()->json([
            'message' => 'Lesson started',
            'progress' => [
                'id' => $progress->id,
                'status' => $progress->status->value,
                'score' => $progress->score,
            ],
        ]);
    }

    public function submitAnswer(SubmitAnswerRequest $request): JsonResponse
    {
        $exercise = \App\Models\Exercise::with('lesson')->findOrFail($request->exercise_id);
        $userAnswer = $this->progressionService->submitAnswer(
            $request->user(),
            $exercise,
            $request->answer
        );

        $score = $this->progressionService->calculateLessonScore(
            $request->user(),
            $exercise->lesson
        );

        return response()->json([
            'answer' => [
                'id' => $userAnswer->id,
                'is_correct' => $userAnswer->is_correct,
                'feedback' => $userAnswer->feedback,
            ],
            'lesson_progress' => [
                'score' => $score,
            ],
        ]);
    }

    public function completeLesson(CompleteLessonRequest $request, int $lessonId): JsonResponse
    {
        $lesson = Lesson::findOrFail($lessonId);
        $progress = $this->progressionService->completeLesson(
            $request->user(),
            $lesson,
            $request->score
        );

        $this->adaptiveService->updateSkillLevels($request->user());

        return response()->json([
            'message' => 'Lesson completed',
            'progress' => [
                'id' => $progress->id,
                'status' => $progress->status->value,
                'score' => $progress->score,
                'completed_at' => $progress->completed_at,
            ],
            'xp_earned' => $this->calculateXpEarned($request->score),
        ]);
    }

    public function overview(): JsonResponse
    {
        $user = request()->user();

        $progress = UserLessonProgress::where('user_id', $user->id)
            ->with(['lesson.module', 'lesson.exercises'])
            ->get();

        $completed = $progress->where('status', 'completed')->count();
        $inProgress = $progress->where('status', 'in_progress')->count();
        $total = $progress->count();
        $averageScore = $progress->where('status', 'completed')->avg('score') ?? 0;

        return response()->json([
            'overview' => [
                'total_lessons' => $total,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'locked' => $total - $completed - $inProgress,
                'average_score' => round($averageScore, 2),
                'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            ],
            'progress' => $progress->map(fn ($p) => [
                'id' => $p->id,
                'status' => $p->status->value,
                'score' => $p->score,
                'lesson' => [
                    'id' => $p->lesson->id,
                    'title' => $p->lesson->title,
                    'type' => $p->lesson->type->value,
                    'module' => $p->lesson->module->title,
                ],
                'completed_at' => $p->completed_at,
            ]),
        ]);
    }

    protected function calculateXpEarned(float $score): int
    {
        return (int) round(10 + (10 * ($score / 100)));
    }
}
