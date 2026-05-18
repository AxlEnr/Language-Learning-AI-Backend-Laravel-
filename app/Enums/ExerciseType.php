<?php

namespace App\Enums;

enum ExerciseType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case FILL_BLANK = 'fill_blank';
    case TRANSLATION = 'translation';
    case SPEAKING = 'speaking';
    case AI_CHAT = 'ai_chat';
}
