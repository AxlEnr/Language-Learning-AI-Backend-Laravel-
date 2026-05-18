<?php

namespace App\Rules;

use App\Enums\ExerciseType;
use App\Models\Exercise;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidExerciseAnswer implements ValidationRule
{
    public function __construct(
        protected int $exerciseId
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exercise = Exercise::find($this->exerciseId);

        if (!$exercise) {
            $fail('The exercise does not exist.');
            return;
        }

        if (empty(trim($value))) {
            $fail('The answer cannot be empty.');
            return;
        }

        if ($exercise->type === ExerciseType::MULTIPLE_CHOICE) {
            $metadata = $exercise->metadata ?? [];
            $optionsCount = count($metadata['options'] ?? []);

            if (!is_numeric($value) || (int) $value < 0 || (int) $value >= $optionsCount) {
                $fail("The answer must be a valid option index (0-{$optionsCount}).");
            }
        }
    }
}
