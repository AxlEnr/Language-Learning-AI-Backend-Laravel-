<?php

namespace App\Http\Requests\Voice;

use Illuminate\Foundation\Http\FormRequest;

class VoiceConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm,ogg', 'max:25000'],
            'language' => ['sometimes', 'string', 'max:5'],
            'voice' => ['sometimes', 'string', 'in:alloy,echo,fable,onyx,nova,shimmer'],
            'format' => ['sometimes', 'string', 'in:mp3,opus,aac,flac,pcm'],
        ];
    }
}