<?php

namespace App\Http\Requests\Progress;

use App\Rules\AccessibleLesson;
use Illuminate\Foundation\Http\FormRequest;

class StartLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => [
                'required',
                'exists:lessons,id',
                new AccessibleLesson($this->user()->id),
            ],
        ];
    }
}
