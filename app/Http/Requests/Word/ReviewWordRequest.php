<?php

namespace App\Http\Requests\Word;

use Illuminate\Foundation\Http\FormRequest;

class ReviewWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'was_correct' => ['required', 'boolean'],
        ];
    }
}
