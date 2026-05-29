<?php

namespace App\Http\Requests\Voice;

use Illuminate\Foundation\Http\FormRequest;

class TextToSpeechRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:4096'],
            'voice' => ['sometimes', 'string', 'in:alloy,echo,fable,onyx,nova,shimmer'],
            'model' => ['sometimes', 'string', 'in:tts-1,tts-1-hd'],
            'format' => ['sometimes', 'string', 'in:mp3,opus,aac,flac,pcm'],
        ];
    }
}