<?php

namespace App\Http\Requests\AI;

use Illuminate\Foundation\Http\FormRequest;

class GenerateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skill' => ['required', 'in:vocabulary,grammar,listening,speaking'],
            'type' => ['required', 'in:multiple_choice,fill_blank,translation,speaking,ai_chat'],
            'topic' => ['nullable', 'string', 'max:255'],
        ];
    }
}
