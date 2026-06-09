<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AI\GenerateContentRequest;
use App\Http\Requests\AI\GenerateExerciseRequest;
use App\Http\Requests\AI\SendMessageRequest;
use App\Http\Requests\AI\StartConversationRequest;
use App\Jobs\GenerateContentJob;
use App\Models\AIConversation;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Word;
use App\Services\AI\AIService;
use App\Services\Adaptive\AdaptiveService;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{
    public function __construct(
        protected AIService $aiService,
        protected AdaptiveService $adaptiveService
    ) {
    }

    public function startConversation(StartConversationRequest $request): JsonResponse
    {
        $context = array_merge([
            'topic' => 'general conversation',
            'difficulty' => 1,
            'language' => $request->user()->targetLanguage?->name ?? 'English',
            'voice_mode' => false,
        ], $request->context ?? []);

        if ($request->filled('topic')) {
            $context['topic'] = $request->topic;
        }

        if ($request->filled('difficulty')) {
            $context['difficulty'] = $request->difficulty;
        }

        $conversation = $this->aiService->startConversationContext($context);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'context' => $conversation->context,
                'created_at' => $conversation->created_at,
            ],
        ], 201);
    }

    public function sendMessage(SendMessageRequest $request, int $conversationId): JsonResponse
    {
        $conversation = AIConversation::where('user_id', $request->user()->id)
            ->findOrFail($conversationId);

        $result = $this->aiService->sendMessage($conversation, $request->message);

        return response()->json([
            'response' => $result['message'],
            'tokens_used' => $result['tokens_used'],
        ]);
    }

    public function getConversation(int $conversationId): JsonResponse
    {
        $conversation = AIConversation::where('user_id', request()->user()->id)
            ->with('messages')
            ->findOrFail($conversationId);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'context' => $conversation->context,
                'messages' => $conversation->messages->map(fn ($msg) => [
                    'id' => $msg->id,
                    'role' => $msg->role->value,
                    'message' => $msg->message,
                    'metadata' => $msg->metadata,
                    'created_at' => $msg->created_at,
                ]),
            ],
        ]);
    }

    public function listConversations(): JsonResponse
    {
        $conversations = request()->user()
            ->aiConversations()
            ->withCount('messages')
            ->latest()
            ->get();

        return response()->json([
            'conversations' => $conversations->map(fn ($conv) => [
                'id' => $conv->id,
                'context' => $conv->context,
                'messages_count' => $conv->messages_count,
                'created_at' => $conv->created_at,
            ]),
        ]);
    }

    public function generateExercise(GenerateExerciseRequest $request): JsonResponse
    {
        $result = $this->adaptiveService->generateAIExercise(
            $request->user(),
            $request->skill,
            $request->type
        );

        return response()->json([
            'exercise' => $result['exercise'],
            'tokens_used' => $result['tokens_used'],
        ]);
    }

    public function recommendLesson(): JsonResponse
    {
        $lesson = $this->adaptiveService->recommendNextLesson(request()->user());

        if (!$lesson) {
            return response()->json([
                'message' => 'No lessons available for recommendation',
            ]);
        }

        return response()->json([
            'recommended_lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type->value,
                'module' => [
                    'id' => $lesson->module->id,
                    'title' => $lesson->module->title,
                ],
            ],
        ]);
    }

    public function generateContent(GenerateContentRequest $request): JsonResponse
    {
        $user   = $request->user();
        $type   = $request->type;
        $langId = $request->language_id;

        $language = \App\Models\Language::find($langId);
        $levelCode = 'A1';

        if ($request->filled('level_id')) {
            $level = \App\Models\Level::find($request->level_id);
            if ($level) {
                $levelCode = $level->code;
            }
        } elseif ($user->level) {
            $levelCode = $user->level->code;
        }

        $params = [
            'type'          => $type,
            'language_id'   => $langId,
            'language_name' => $language?->name ?? 'Unknown',
            'level_code'    => $levelCode,
            'quantity'      => $request->quantity ?? 1,
            'module_id'     => $request->module_id,
            'lesson_type'   => $request->lesson_type,
            'exercise_type' => $request->exercise_type,
        ];

        $params = array_merge($params, $this->buildCompactContext($request));

        GenerateContentJob::dispatchSync($params);

        $qty = $request->quantity ?? 1;
        return response()->json([
            'message'  => "AI generated {$qty} {$type}(s) for {$params['language_name']}",
            'type'     => $type,
            'quantity' => $request->quantity ?? 1,
            'language' => $params['language_name'],
        ]);
    }

    protected function buildCompactContext(GenerateContentRequest $request): array
    {
        $type = $request->type;
        $context = [];

        if (($type === 'lesson' || $type === 'exercise') && $request->module_id) {
            $module = Module::with('lessons')->find($request->module_id);
            if ($module) {
                $context['module_title'] = $module->title;
                $context['existing_lessons'] = $module->lessons
                    ->map(fn ($l) => substr($l->type->value, 0, 1) . ':' . $l->title)
                    ->implode(', ');
            }
        }

        if ($type === 'exercise' && $request->lesson_id) {
            $lesson = Lesson::with('exercises')->find($request->lesson_id);
            if ($lesson) {
                $context['lesson_title'] = $lesson->title;
                $context['lesson_type_context'] = $lesson->type->value;
                $context['existing_exercises'] = $lesson->exercises
                    ->map(fn ($e) => substr($e->type->value, 0, 2) . ':' . mb_substr($e->prompt, 0, 30))
                    ->implode(', ');
            }
        }

        if ($type === 'words') {
            $sample = Word::where('language_id', $request->language_id)
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->map(fn ($w) => $w->word . '(' . $w->meaning . ')')
                ->implode(', ');
            $context['existing_words'] = $sample;
        }

        return $context;
    }
}
