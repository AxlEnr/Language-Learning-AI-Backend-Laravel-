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
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
        $this->model = config('services.openai.model') ?? 'gpt-4o-mini';
        $this->baseUrl = config('services.openai.base_url') ?? 'https://api.openai.com/v1';
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

    public function generateContent(array $params): array
    {
        $prompt = $this->buildContentGenerationPrompt($params);

        $response = $this->callOpenAI([
            ['role' => 'system', 'content' => 'Generate language learning content. Output ONLY valid JSON. No explanations.'],
            ['role' => 'user', 'content' => $prompt],
        ], 4000);

        $content = $response['choices'][0]['message']['content'] ?? '';

        return [
            'content'     => $this->parseJsonResponse($content),
            'raw_content' => $content,
            'tokens_used' => $response['usage']['total_tokens'] ?? 0,
        ];
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
        $voiceMode = !empty($context['voice_mode']) && $context['voice_mode'];

        $base = "You are a helpful language learning assistant teaching {$language}. 
                The student's level is {$difficulty} out of 5 (1=beginner, 5=advanced).
                Current topic: {$topic}.
                
                Rules:
                - Always respond in the target language with occasional translations
                - Correct mistakes gently and explain why
                - Keep responses appropriate for the difficulty level
                - Encourage the student
                - If the topic is specified, stay focused on it";

        if ($voiceMode) {
            $base .= "

                VOICE LESSON MODE RULES:
                - Keep responses SHORT and conversational (2-3 sentences max) since they will be spoken aloud
                - Use natural, spoken language rather than formal written language
                - When correcting pronunciation mistakes, provide clear phonetic guidance
                - Ask follow-up questions to encourage the student to keep speaking
                - Occasionally provide pronunciation tips (e.g., 'Remember, the TH sound is made by placing your tongue between your teeth')
                - Use simple vocabulary appropriate for the difficulty level since the student is listening, not reading
                - If the student makes a grammatical error in their speech, gently repeat the correct version naturally in your response";
        }

        return $base;
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

    protected function buildContentGenerationPrompt(array $params): string
    {
        $type     = $params['type'];
        $language = $params['language_name'] ?? 'English';
        $level    = $params['level_code'] ?? 'A1';
        $quantity = $params['quantity'] ?? 1;

        return match ($type) {
            'lesson'   => $this->buildLessonPrompt($params, $language, $level, $quantity),
            'exercise' => $this->buildExercisePrompt($params, $language, $level, $quantity),
            'words'    => $this->buildWordsPrompt($params, $language, $level, $quantity),
            default    => '{}',
        };
    }

    protected function buildLessonPrompt(array $p, string $lang, string $lvl, int $qty): string
    {
        $lessonType = $p['lesson_type'] ?? 'vocabulary';
        $moduleTitle = $p['module_title'] ?? '';
        $existing = $p['existing_lessons'] ?? '';

        return "Gen {$qty} {$lessonType} lessons for {$lang} (CEFR {$lvl}). Module: \"{$moduleTitle}\".
Existing: {$existing}

Return ONLY JSON array:
[{\"title\":\"...\",\"exercises\":[{\"type\":\"mc|fb|tr|sp|ac\",\"prompt\":\"...\",\"metadata\":{...}}]}]

Exercise metadata by type:
mc: {\"options\":[\"a\",\"b\",\"c\",\"d\"],\"correct_option\":0,\"hints\":[\"...\"]}
fb: {\"correct_answer\":\"...\",\"hints\":[\"...\"]}
tr: {\"source_text\":\"...\",\"accepted_answers\":[\"...\"]}
sp: {\"expected_phrase\":\"...\",\"pronunciation_tips\":\"...\"}
ac: {\"scenario\":\"...\"}";
    }

    protected function buildExercisePrompt(array $p, string $lang, string $lvl, int $qty): string
    {
        $exType    = $p['exercise_type'] ?? 'multiple_choice';
        $lessonTitle = $p['lesson_title'] ?? '';
        $lessonType  = $p['lesson_type_context'] ?? '';
        $existing = $p['existing_exercises'] ?? '';

        return "Gen {$qty} {$exType} exercises for {$lang} (CEFR {$lvl}).
Lesson: \"{$lessonTitle}\" ({$lessonType}).
Existing exercises: {$existing}

Return ONLY JSON array:
[{\"type\":\"{$exType}\",\"prompt\":\"...\",\"metadata\":{...}}]

Metadata by type:
mc: {\"options\":[\"a\",\"b\",\"c\",\"d\"],\"correct_option\":0,\"hints\":[\"...\"]}
fb: {\"correct_answer\":\"...\",\"hints\":[\"...\"]}
tr: {\"source_text\":\"...\",\"accepted_answers\":[\"...\"]}
sp: {\"expected_phrase\":\"...\",\"pronunciation_tips\":\"...\"}
ac: {\"scenario\":\"...\"}";
    }

    protected function buildWordsPrompt(array $p, string $lang, string $lvl, int $qty): string
    {
        $existing = $p['existing_words'] ?? '';

        return "Gen {$qty} vocabulary words for {$lang} (CEFR {$lvl}).
Existing words (sample): {$existing}

Return ONLY JSON array:
[{\"word\":\"...\",\"meaning\":\"...\",\"example_sentence\":\"...\"}]";
    }

    protected function callOpenAI(array $messages, int $maxTokens = 1000): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->post("{$this->baseUrl}/chat/completions", [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => $maxTokens,
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
        $decoded = json_decode($content, true);
        if ($decoded !== null) {
            return $decoded;
        }

        if (preg_match('/```(?:json)?\s*(\[.*\]|\{.*\})\s*```/s', $content, $matches)) {
            return json_decode($matches[1], true) ?? [];
        }

        if (preg_match('/(\[.*\]|\{.*\})/s', $content, $matches)) {
            return json_decode($matches[0], true) ?? [];
        }

        return [];
    }

    protected function generateTranslationFeedback(string $userAnswer, string $sourceText, array $acceptedAnswers): string
    {
        $bestMatch = $acceptedAnswers[0] ?? '';

        return "Not quite. The correct translation is: \"{$bestMatch}\". Your answer: \"{$userAnswer}\". Keep practicing!";
    }
}
