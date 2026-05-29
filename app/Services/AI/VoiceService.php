<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VoiceService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }

    public function speechToText(string $audioPath, string $language = 'en'): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->attach(
            'file',
            file_get_contents($audioPath),
            basename($audioPath)
        )->post("{$this->baseUrl}/audio/transcriptions", [
            'model' => 'whisper-1',
            'language' => $language,
            'response_format' => 'verbose_json',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Whisper STT error: ' . ($response->json('error.message') ?? $response->body()));
        }

        $data = $response->json();

        return [
            'text' => $data['text'] ?? '',
            'language' => $data['language'] ?? $language,
            'duration' => $data['duration'] ?? 0,
            'segments' => $data['segments'] ?? [],
        ];
    }

    public function textToSpeech(string $text, string $voice = 'alloy', string $model = null, string $format = 'mp3'): string
    {
        $model = $model ?? config('services.openai.tts_model', 'tts-1');

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/audio/speech", [
            'model' => $model,
            'input' => $text,
            'voice' => $voice,
            'response_format' => $format,
            'speed' => 0.9,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenAI TTS error: ' . ($response->json('error.message') ?? 'Unknown error'));
        }

        $filename = 'voice/tts_' . uniqid() . ".{$format}";
        Storage::put($filename, $response->body());

        return $filename;
    }

    public function evaluatePronunciation(string $audioPath, string $expectedText, string $language = 'en'): array
    {
        $sttResult = $this->speechToText($audioPath, $language);
        $recognizedText = $sttResult['text'];

        $similarity = $this->calculateSimilarity($recognizedText, $expectedText);

        $feedback = $this->generatePronunciationFeedback($recognizedText, $expectedText, $similarity);

        return [
            'recognized_text' => $recognizedText,
            'expected_text' => $expectedText,
            'similarity_score' => $similarity,
            'is_correct' => $similarity >= 80,
            'feedback' => $feedback,
            'duration' => $sttResult['duration'],
        ];
    }

    protected function calculateSimilarity(string $recognized, string $expected): float
    {
        $recognized = strtolower(trim($recognized));
        $expected = strtolower(trim($expected));

        if ($recognized === $expected) {
            return 100.0;
        }

        similar_text($recognized, $expected, $percent);

        $recognizedWords = explode(' ', $recognized);
        $expectedWords = explode(' ', $expected);
        $expectedWordSet = array_flip($expectedWords);
        $matchedWords = 0;

        foreach ($recognizedWords as $word) {
            if (isset($expectedWordSet[$word])) {
                $matchedWords++;
            }
        }

        $wordAccuracy = count($expectedWords) > 0
            ? ($matchedWords / count($expectedWords)) * 100
            : 0;

        return round(($percent * 0.5) + ($wordAccuracy * 0.5), 1);
    }

    protected function generatePronunciationFeedback(string $recognized, string $expected, float $similarity): string
    {
        if ($similarity >= 95) {
            return 'Excellent pronunciation!';
        }

        if ($similarity >= 80) {
            return 'Good pronunciation! Small details to improve.';
        }

        if ($similarity >= 60) {
            $diff = $this->getWordDifferences($recognized, $expected);
            return 'Keep practicing. Focus on: ' . implode(', ', $diff);
        }

        return 'Try again. Listen to the correct pronunciation and repeat slowly.';
    }

    protected function getWordDifferences(string $recognized, string $expected): array
    {
        $recognizedWords = array_unique(explode(' ', strtolower(trim($recognized))));
        $expectedWords = array_unique(explode(' ', strtolower(trim($expected))));

        return array_values(array_diff($expectedWords, $recognizedWords));
    }
}