<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voice\PronunciationRequest;
use App\Http\Requests\Voice\SpeechToTextRequest;
use App\Http\Requests\Voice\TextToSpeechRequest;
use App\Http\Requests\Voice\VoiceConversationRequest;
use App\Models\AIConversation;
use App\Services\AI\AIService;
use App\Services\AI\VoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VoiceController extends Controller
{
    public function __construct(
        protected VoiceService $voiceService,
        protected AIService $aiService
    ) {
    }

    public function speechToText(SpeechToTextRequest $request): JsonResponse
    {
        $file = $request->file('audio');
        $path = $file->store('voice/stt', 'local');
        $fullPath = Storage::path($path);

        try {
            $result = $this->voiceService->speechToText(
                $fullPath,
                $request->input('language', 'en')
            );
        } finally {
            Storage::delete($path);
        }

        return response()->json($result);
    }

    public function textToSpeech(TextToSpeechRequest $request): JsonResponse
    {
        $filename = $this->voiceService->textToSpeech(
            $request->input('text'),
            $request->input('voice', 'alloy'),
            $request->input('model'),
            $request->input('format', 'mp3')
        );

        return response()->json([
            'audio_url' => url("/api/v1/voice/audio/{$filename}"),
            'format' => $request->input('format', 'mp3'),
        ]);
    }

    public function serveAudio(string $path): mixed
    {
        if (!Storage::exists($path)) {
            abort(404);
        }

        $mimeType = match (pathinfo($path, PATHINFO_EXTENSION)) {
            'mp3' => 'audio/mpeg',
            'opus' => 'audio/opus',
            'aac' => 'audio/aac',
            'flac' => 'audio/flac',
            'pcm' => 'audio/pcm',
            default => 'application/octet-stream',
        };

        return response()->file(Storage::path($path), [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, must-revalidate',
        ])->deleteFileAfterSend(true);
    }

    public function evaluatePronunciation(PronunciationRequest $request): JsonResponse
    {
        $file = $request->file('audio');
        $path = $file->store('voice/pronunciation', 'local');
        $fullPath = Storage::path($path);

        try {
            $result = $this->voiceService->evaluatePronunciation(
                $fullPath,
                $request->input('expected_text'),
                $request->input('language', 'en')
            );
        } finally {
            Storage::delete($path);
        }

        return response()->json($result);
    }

    public function voiceConversation(VoiceConversationRequest $request, int $conversationId): JsonResponse
    {
        $conversation = AIConversation::where('user_id', $request->user()->id)
            ->findOrFail($conversationId);

        $file = $request->file('audio');
        $path = $file->store('voice/conversation', 'local');
        $fullPath = Storage::path($path);

        try {
            $sttResult = $this->voiceService->speechToText(
                $fullPath,
                $request->input('language', $conversation->context['language_code'] ?? 'en')
            );
        } finally {
            Storage::delete($path);
        }

        $userText = $sttResult['text'];

        $aiResult = $this->aiService->sendMessage($conversation, $userText);
        $aiResponse = $aiResult['message'];

        $voice = $request->input('voice', 'alloy');
        $ttsFormat = $request->input('format', 'mp3');

        $audioPath = $this->voiceService->textToSpeech($aiResponse, $voice, null, $ttsFormat);

        return response()->json([
            'recognized_text' => $userText,
            'ai_response' => $aiResponse,
            'audio_url' => url("/api/v1/voice/audio/" . $audioPath),
            'voice' => $voice,
            'duration' => $sttResult['duration'],
            'tokens_used' => $aiResult['tokens_used'],
        ]);
    }
}