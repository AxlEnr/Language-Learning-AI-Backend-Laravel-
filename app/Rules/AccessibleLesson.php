<?php

namespace App\Rules;

use App\Enums\ProgressStatus;
use App\Models\Lesson;
use App\Models\UserLessonProgress;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AccessibleLesson implements ValidationRule
{
    public function __construct(
        protected int $userId
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lesson = Lesson::find($value);

        if (!$lesson) {
            $fail('The lesson does not exist.');
            return;
        }

        $progress = UserLessonProgress::where('user_id', $this->userId)
            ->where('lesson_id', $value)
            ->first();

        if ($progress && $progress->status === ProgressStatus::LOCKED) {
            $fail('This lesson is locked. Complete previous lessons first.');
        }

        if (!$progress) {
            $previousLesson = Lesson::where('module_id', $lesson->module_id)
                ->where('order_index', '<', $lesson->order_index)
                ->orderByDesc('order_index')
                ->first();

            if ($previousLesson) {
                $previousProgress = UserLessonProgress::where('user_id', $this->userId)
                    ->where('lesson_id', $previousLesson->id)
                    ->where('status', 'completed')
                    ->first();

                if (!$previousProgress) {
                    $fail('You must complete the previous lesson first.');
                }
            }
        }
    }
}
