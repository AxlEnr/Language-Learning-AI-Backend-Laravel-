<?php

namespace App\Http\Requests\AI;

use Illuminate\Foundation\Http\FormRequest;

class GenerateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'          => ['required', 'in:lesson,exercise,words'],
            'language_id'   => ['required', 'exists:languages,id'],
            'quantity'      => ['sometimes', 'integer', 'min:1', 'max:20'],
            'module_id'     => ['required_if:type,lesson', 'nullable', 'required_if:type,exercise', 'exists:modules,id'],
            'lesson_id'     => ['required_if:type,exercise', 'nullable', 'exists:lessons,id'],
            'lesson_type'   => ['required_if:type,lesson', 'nullable', 'in:vocabulary,grammar,listening,speaking'],
            'exercise_type' => ['required_if:type,exercise', 'nullable', 'in:multiple_choice,fill_blank,translation,speaking,ai_chat'],
            'level_id'      => ['sometimes', 'exists:levels,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in'                => 'Type must be one of: lesson, exercise, words',
            'module_id.required_if'  => 'Module is required when generating lessons or exercises',
            'lesson_id.required_if'  => 'Lesson is required when generating exercises',
            'lesson_type.required_if'=> 'Lesson type is required when generating lessons',
            'exercise_type.required_if' => 'Exercise type is required when generating exercises',
        ];
    }
}
