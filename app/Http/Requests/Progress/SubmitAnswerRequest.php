<?php

namespace App\Http\Requests\Progress;

use App\Rules\ValidExerciseAnswer;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => ['required', 'exists:exercises,id'],
            'answer' => ['required', 'string', new ValidExerciseAnswer($this->exercise_id)],
        ];
    }
}
