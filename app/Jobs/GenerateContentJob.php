<?php

namespace App\Jobs;

use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Word;
use App\Services\AI\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $params
    ) {}

    public function handle(AIService $aiService): void
    {
        try {
            $result = $aiService->generateContent($this->params);
            $content = $result['content'] ?? [];

            if (empty($content)) {
                Log::warning('GenerateContentJob: AI returned empty content', $this->params);
                return;
            }

            $content = $this->normalizeContent($content);

            match ($this->params['type']) {
                'lesson'   => $this->persistLessons($content),
                'exercise' => $this->persistExercises($content),
                'words'    => $this->persistWords($content),
                default    => null,
            };
        } catch (\Throwable $e) {
            Log::error('GenerateContentJob failed: ' . $e->getMessage(), [
                'params' => $this->params,
                'trace'  => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    protected function normalizeContent(array $content): array
    {
        if (array_is_list($content)) {
            return $content;
        }

        if (isset($content['title']) || isset($content['word'])) {
            return [$content];
        }

        return array_values($content);
    }

    protected function persistLessons(array $lessons): void
    {
        $module = Module::findOrFail($this->params['module_id']);
        $maxOrder = $module->lessons()->max('order_index') ?? 0;

        foreach ($lessons as $i => $lessonData) {
            if (empty($lessonData['title'])) {
                continue;
            }

            $lesson = $module->lessons()->create([
                'title'       => $lessonData['title'],
                'type'        => $this->params['lesson_type'],
                'order_index' => $maxOrder + $i + 1,
            ]);

            foreach ($lessonData['exercises'] ?? [] as $exerciseData) {
                if (empty($exerciseData['prompt'])) {
                    continue;
                }

                Exercise::create([
                    'lesson_id' => $lesson->id,
                    'type'      => $this->normalizeExerciseType($exerciseData['type'] ?? 'mc'),
                    'prompt'    => $exerciseData['prompt'],
                    'metadata'  => $exerciseData['metadata'] ?? [],
                ]);
            }
        }
    }

    protected function persistExercises(array $exercises): void
    {
        $lesson = Lesson::findOrFail($this->params['lesson_id']);

        foreach ($exercises as $exerciseData) {
            if (empty($exerciseData['prompt'])) {
                continue;
            }

            Exercise::create([
                'lesson_id' => $lesson->id,
                'type'      => $this->normalizeExerciseType(
                    $exerciseData['type'] ?? $this->params['exercise_type'] ?? 'mc'
                ),
                'prompt'    => $exerciseData['prompt'],
                'metadata'  => $exerciseData['metadata'] ?? [],
            ]);
        }
    }

    protected function normalizeExerciseType(string $type): string
    {
        return match ($type) {
            'mc' => 'multiple_choice',
            'fb' => 'fill_blank',
            'tr' => 'translation',
            'sp' => 'speaking',
            'ac' => 'ai_chat',
            default => $type,
        };
    }

    protected function persistWords(array $words): void
    {
        $languageId = $this->params['language_id'];

        foreach ($words as $wordData) {
            if (empty($wordData['word'])) {
                continue;
            }

            Word::firstOrCreate(
                [
                    'language_id' => $languageId,
                    'word'        => $wordData['word'],
                ],
                [
                    'meaning'          => $wordData['meaning'] ?? '',
                    'example_sentence' => $wordData['example_sentence'] ?? '',
                ]
            );
        }
    }
}
