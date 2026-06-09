<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Word;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(Request $request): JsonResponse
    {
        $query = User::with(['nativeLanguage', 'targetLanguage', 'level'])
            ->select(['id', 'name', 'email', 'role', 'native_language_id', 'target_language_id', 'level_id', 'created_at']);

        if ($term = $request->string('term')->trim()) {
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'ilike', "%{$term}%")
                  ->orWhere('email', 'ilike', "%{$term}%")
                  ->orWhere('role', 'ilike', "%{$term}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ],
        ]);
    }

    public function lessons(Request $request): JsonResponse
    {
        $query = Lesson::with(['module.language', 'module.level'])
            ->withCount('exercises');

        if ($term = $request->string('term')->trim()) {
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'ilike', "%{$term}%")
                  ->orWhere('type', 'ilike', "%{$term}%")
                  ->orWhereHas('module', function ($mq) use ($term): void {
                      $mq->where('title', 'ilike', "%{$term}%")
                         ->orWhereHas('language', fn ($lq) => $lq->where('name', 'ilike', "%{$term}%"));
                  });
            });
        }

        $lessons = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $lessons->map(fn ($l) => [
                'id'              => $l->id,
                'title'           => $l->title,
                'type'            => $l->type->value,
                'order_index'     => $l->order_index,
                'exercises_count' => $l->exercises_count,
                'module'          => [
                    'id'    => $l->module?->id,
                    'title' => $l->module?->title,
                ],
                'language'    => $l->module?->language?->name,
                'level'       => $l->module?->level?->code,
                'created_at'  => $l->created_at,
            ]),
            'meta' => [
                'current_page' => $lessons->currentPage(),
                'last_page'    => $lessons->lastPage(),
                'per_page'     => $lessons->perPage(),
                'total'        => $lessons->total(),
            ],
        ]);
    }

    public function exercises(Request $request): JsonResponse
    {
        $query = Exercise::with(['lesson.module']);

        if ($term = $request->string('term')->trim()) {
            $query->where(function ($q) use ($term): void {
                $q->where('prompt', 'ilike', "%{$term}%")
                  ->orWhere('type', 'ilike', "%{$term}%")
                  ->orWhereHas('lesson', function ($lq) use ($term): void {
                      $lq->where('title', 'ilike', "%{$term}%")
                         ->orWhereHas('module', fn ($mq) => $mq->where('title', 'ilike', "%{$term}%"));
                  });
            });
        }

        $exercises = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $exercises->map(fn ($e) => [
                'id'         => $e->id,
                'type'       => $e->type->value,
                'prompt'     => mb_substr($e->prompt, 0, 80),
                'lesson'     => [
                    'id'    => $e->lesson?->id,
                    'title' => $e->lesson?->title,
                ],
                'module'     => $e->lesson?->module?->title,
                'created_at' => $e->created_at,
            ]),
            'meta' => [
                'current_page' => $exercises->currentPage(),
                'last_page'    => $exercises->lastPage(),
                'per_page'     => $exercises->perPage(),
                'total'        => $exercises->total(),
            ],
        ]);
    }

    public function words(Request $request): JsonResponse
    {
        $query = Word::with('language');

        if ($term = $request->string('term')->trim()) {
            $query->where(function ($q) use ($term): void {
                $q->where('word', 'ilike', "%{$term}%")
                  ->orWhere('meaning', 'ilike', "%{$term}%")
                  ->orWhere('example_sentence', 'ilike', "%{$term}%")
                  ->orWhereHas('language', fn ($lq) => $lq->where('name', 'ilike', "%{$term}%"));
            });
        }

        $words = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $words->map(fn ($w) => [
                'id'               => $w->id,
                'word'             => $w->word,
                'meaning'          => $w->meaning,
                'example_sentence' => mb_substr($w->example_sentence ?? '', 0, 100),
                'language'         => $w->language?->name,
                'language_code'    => $w->language?->code,
                'created_at'       => $w->created_at,
            ]),
            'meta' => [
                'current_page' => $words->currentPage(),
                'last_page'    => $words->lastPage(),
                'per_page'     => $words->perPage(),
                'total'        => $words->total(),
            ],
        ]);
    }

    public function destroyLesson(int $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted']);
    }

    public function destroyExercise(int $id): JsonResponse
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return response()->json(['message' => 'Exercise deleted']);
    }

    public function destroyWord(int $id): JsonResponse
    {
        $word = Word::findOrFail($id);
        $word->delete();

        return response()->json(['message' => 'Word deleted']);
    }
}
