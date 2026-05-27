<?php

namespace App\Services\AI;

use App\Enums\AIRole;
use App\Interfaces\AIServiceInterface;
use App\Models\AIConversation;
use App\Models\AIMessage;
use Illuminate\Support\Facades\Http;

class AIService implements AIServiceInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
        $this->model = config('services.openai.model') ?? 'gpt-4o-mini';
    }

    public function sendMessage(AIConversation $conversation, string $message): array
    {
        $history = $this->buildMessageHistory($conversation);

        $history[] = ['role' => 'user', 'content' => $message];

        $systemPrompt = $this->buildSystemPrompt($conversation->context ?? []);

        $response = $this->callOpenAI([
            ['role' => 'system', 'content' => $systemPrompt],
            ...$history,
        ]);

        $aiResponse = $response['choices'][0]['message']['content'] ?? '';

        $this->addMessage($conversation, AIRole::USER->value, $message, []);
        $this->addMessage($conversation, AIRole::ASSISTANT->value, $aiResponse, [
            'tokens_used' => $response['usage']['total_tokens'] ?? 0,
            'model' => $this->model,
        ]);

        return [
            'message' => $aiResponse,
            'tokens_used' => $response['usage']['total_tokens'] ?? 0,
        ];
    }

    public function generateExerciseFeedback(string $userAnswer, array $exerciseMetadata): array
    {
        $prompt = $this->buildFeedbackPrompt($userAnswer, $exerciseMetadata);

        $response = $this->callOpenAI([
            ['role' => 'system', 'content' => 'You are a language learning assistant. Provide constructive feedback on the user\'s answer.'],
            ['role' => 'user', 'content' => $prompt],
        ]);

        $content = $response['choices'][0]['message']['content'] ?? '';

        return [
            'feedback' => $content,
            'is_correct' => $this->extractCorrectness($content),
            'tokens_used' => $response['usage']['total_tokens'] ?? 0,
        ];
    }

    public function generateAdaptiveExercise(array $context): array
    {
        $prompt = $this->buildAdaptiveExercisePrompt($context);

        $response = $this->callOpenAI([
            ['role' => 'system', 'content' => 'You are a language learning exercise generator. Create exercises in valid JSON format.'],
            ['role' => 'user', 'content' => $prompt],
        ]);

        $content = $response['choices'][0]['message']['content'] ?? '';

        return [
            'exercise' => $this->parseJsonResponse($content),
            'tokens_used' => $response['usage']['total_tokens'] ?? 0,
        ];
    }

    public function evaluateTranslation(string $userAnswer, string $sourceText, array $acceptedAnswers): array
    {
        $normalizedAnswer = strtolower(trim($userAnswer));
        $isCorrect = in_array($normalizedAnswer, array_map('strtolower', $acceptedAnswers), true);

        $feedback = $isCorrect
            ? 'Correct! Well done!'
            : $this->generateTranslationFeedback($userAnswer, $sourceText, $acceptedAnswers);

        return [
            'is_correct' => $isCorrect,
            'feedback' => $feedback,
        ];
    }

    public function startConversationContext(array $context): AIConversation
    {
        return AIConversation::create([
            'user_id' => auth()->id(),
            'context' => $context,
        ]);
    }

    public function addMessage(AIConversation $conversation, string $role, string $message, array $metadata = []): AIMessage
    {
        return $conversation->messages()->create([
            'role' => $role,
            'message' => $message,
            'metadata' => $metadata,
        ]);
    }

    protected function buildMessageHistory(AIConversation $conversation): array
    {
        return $conversation->messages()
            ->limit(20)
            ->orderBy('created_at', 'desc')
            ->get()
            ->reverse()
            ->map(fn (AIMessage $msg) => [
                'role' => $msg->role->value,
                'content' => $msg->message,
            ])
            ->values()
            ->toArray();
    }

    protected function buildSystemPrompt(array $context): string
    {
        $language = $context['language'] ?? 'English';
        $difficulty = $context['difficulty'] ?? 1;
        $topic = $context['topic'] ?? 'general conversation';

        return "You are a helpful language learning assistant teaching {$language}. 
                The student's level is {$difficulty} out of 5 (1=beginner, 5=advanced).
                Current topic: {$topic}.
                
                Rules:
                - Always respond in the target language with occasional translations
                - Correct mistakes gently and explain why
                - Keep responses appropriate for the difficulty level
                - Encourage the student
                - If the topic is specified, stay focused on it";
    }

    protected function buildFeedbackPrompt(string $userAnswer, array $exerciseMetadata): string
    {
        $metadata = $exerciseMetadata['prompt'] ?? 'N/A';
        return "Evaluate this language learning exercise answer:
        
        Exercise: {$metadata}
        User's answer: {$userAnswer}
        Expected: " . ($exerciseMetadata['correct_answer'] ?? 'N/A') . "
        
        Provide feedback and indicate if the answer is CORRECT or INCORRECT at the beginning.";
    }

    protected function buildAdaptiveExercisePrompt(array $context): string
    {
        $language = $context['language'] ?? 'English';
        $level = $context['level'] ?? 'A1';
        $skill = $context['skill'] ?? 'vocabulary';
        $type = $context['type'] ?? 'multiple_choice';
        $topic = $context['topic'] ?? 'daily life';
        return "Generate a language learning exercise with these parameters:
        
        Language: {$language}
        Level: {$level}
        Skill: {$skill}
        Type: {$type}
        Topic: {$topic}
        
        Return ONLY valid JSON with this structure:
        {
            \"prompt\": \"exercise question\",
            \"metadata\": {
                \"options\": [\"option1\", \"option2\", \"option3\", \"option4\"],
                \"correct_option\": 0,
                \"hints\": [\"hint1\", \"hint2\"]
            }
        }";
    }

    protected function callOpenAI(array $messages): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('AI service error: ' . ($response->json('error.message') ?? $response->body()));
        }

        return $response->json();
    }

    protected function extractCorrectness(string $content): bool
    {
        return preg_match('/\bCORRECT\b/i', $content) === 1
            && preg_match('/\bINCORRECT\b/i', $content) === 0;
    }

    protected function parseJsonResponse(string $content): array
    {
        $json = preg_match('/\{.*\}/s', $content, $matches) ? $matches[0] : '{}';

        return json_decode($json, true) ?? [];
    }

    protected function generateTranslationFeedback(string $userAnswer, string $sourceText, array $acceptedAnswers): string
    {
        $bestMatch = $acceptedAnswers[0] ?? '';

        return "Not quite. The correct translation is: \"{$bestMatch}\". Your answer: \"{$userAnswer}\". Keep practicing!";
    }
}
