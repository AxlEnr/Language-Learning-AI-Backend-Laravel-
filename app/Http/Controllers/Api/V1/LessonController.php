<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    public function show(string $id): JsonResponse
    {
        $lesson = Lesson::with(['module.language', 'module.level', 'exercises'])->findOrFail($id);

        return response()->json([
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type->value,
                'order_index' => $lesson->order_index,
                'module' => [
                    'id' => $lesson->module->id,
                    'title' => $lesson->module->title,
                    'language' => $lesson->module->language->name,
                    'level' => $lesson->module->level->code,
                ],
                'exercises' => $lesson->exercises->map(fn ($exercise) => [
                    'id' => $exercise->id,
                    'type' => $exercise->type->value,
                    'prompt' => $exercise->prompt,
                    'metadata' => $exercise->metadata,
                ]),
            ],
        ]);
    }
}
