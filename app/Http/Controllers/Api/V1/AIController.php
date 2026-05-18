<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AI\GenerateExerciseRequest;
use App\Http\Requests\AI\SendMessageRequest;
use App\Http\Requests\AI\StartConversationRequest;
use App\Models\AIConversation;
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
}
