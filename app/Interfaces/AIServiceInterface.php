<?php

namespace App\Interfaces;

use App\Models\AIConversation;
use App\Models\AIMessage;

interface AIServiceInterface
{
    public function sendMessage(AIConversation $conversation, string $message): array;

    public function generateExerciseFeedback(string $userAnswer, array $exerciseMetadata): array;

    public function generateAdaptiveExercise(array $context): array;

    public function evaluateTranslation(string $userAnswer, string $sourceText, array $acceptedAnswers): array;

    public function startConversationContext(array $context): AIConversation;

    public function addMessage(AIConversation $conversation, string $role, string $message, array $metadata = []): AIMessage;
}
