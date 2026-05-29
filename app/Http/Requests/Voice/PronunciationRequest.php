<?php

namespace App\Http\Requests\Voice;

use Illuminate\Foundation\Http\FormRequest;

class PronunciationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm,ogg', 'max:25000'],
            'expected_text' => ['required', 'string', 'max:1000'],
            'language' => ['sometimes', 'string', 'max:5'],
        ];
    }
}