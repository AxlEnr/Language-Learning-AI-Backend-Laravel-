<?php

namespace App\Http\Requests\AI;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['nullable', 'integer', 'min:1', 'max:5'],
            'context' => ['nullable', 'array'],
            'voice_mode' => ['sometimes', 'boolean'],
        ];
    }
}
