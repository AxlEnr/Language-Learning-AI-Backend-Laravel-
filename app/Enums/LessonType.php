<?php

namespace App\Enums;

enum LessonType: string
{
    case VOCABULARY = 'vocabulary';
    case GRAMMAR = 'grammar';
    case LISTENING = 'listening';
    case SPEAKING = 'speaking';
}
