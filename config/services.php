<?php

return [
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'tts_model' => env('OPENAI_TTS_MODEL', 'tts-1'),
        'stt_model' => env('OPENAI_STT_MODEL', 'whisper-1'),
    ],
];
