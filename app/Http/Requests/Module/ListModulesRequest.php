<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class ListModulesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'language_id' => ['nullable', 'exists:languages,id'],
            'level_id' => ['nullable', 'exists:levels,id'],
        ];
    }
}
