<?php

namespace App\Enums;

enum SkillType: string
{
    case VOCABULARY = 'vocabulary';
    case GRAMMAR = 'grammar';
    case LISTENING = 'listening';
    case SPEAKING = 'speaking';
}
