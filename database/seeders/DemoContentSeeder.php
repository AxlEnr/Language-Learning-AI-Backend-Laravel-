<?php

namespace Database\Seeders;

use App\Enums\ExerciseType;
use App\Enums\LessonType;
use App\Models\Exercise;
use App\Models\Language;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Module;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $spanish = Language::where('code', 'es')->first();
        $a1 = Level::where('code', 'A1')->first();

        if (!$english || !$a1) {
            return;
        }

        $module = Module::firstOrCreate([
            'language_id' => $english->id,
            'level_id' => $a1->id,
            'order_index' => 1,
        ], [
            'title' => 'Getting Started',
            'description' => 'Learn the basics of English communication',
        ]);

        $lessons = [
            [
                'title' => 'Basic Greetings',
                'type' => LessonType::VOCABULARY,
                'order_index' => 1,
                'exercises' => [
                    [
                        'type' => ExerciseType::MULTIPLE_CHOICE,
                        'prompt' => 'What does "Hello" mean?',
                        'metadata' => [
                            'options' => ['Adiós', 'Hola', 'Gracias', 'Por favor'],
                            'correct_option' => 1,
                        ],
                    ],
                    [
                        'type' => ExerciseType::TRANSLATION,
                        'prompt' => 'Translate "Hola" to English',
                        'metadata' => [
                            'source_text' => 'Hola',
                            'target_language' => 'en',
                            'accepted_answers' => ['hello', 'hi'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Introducing Yourself',
                'type' => LessonType::SPEAKING,
                'order_index' => 2,
                'exercises' => [
                    [
                        'type' => ExerciseType::AI_CHAT,
                        'prompt' => 'Practice introducing yourself. Tell me your name and where you are from.',
                        'metadata' => [
                            'topic' => 'introductions',
                            'difficulty' => 1,
                            'expected_elements' => ['name', 'origin'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Common Verbs',
                'type' => LessonType::GRAMMAR,
                'order_index' => 3,
                'exercises' => [
                    [
                        'type' => ExerciseType::FILL_BLANK,
                        'prompt' => 'Complete the sentence: "I ___ a student."',
                        'metadata' => [
                            'sentence' => 'I ___ a student.',
                            'blank_word' => 'am',
                            'hints' => ['Verb to be', 'First person singular'],
                        ],
                    ],
                    [
                        'type' => ExerciseType::MULTIPLE_CHOICE,
                        'prompt' => 'Which is the correct form? "She ___ to school."',
                        'metadata' => [
                            'options' => ['go', 'goes', 'going', 'gone'],
                            'correct_option' => 1,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($lessons as $lessonData) {
            $exercises = $lessonData['exercises'];
            unset($lessonData['exercises']);

            $lesson = Lesson::firstOrCreate([
                'module_id' => $module->id,
                'order_index' => $lessonData['order_index'],
            ], [
                ...$lessonData,
                'type' => $lessonData['type']->value,
            ]);

            foreach ($exercises as $exerciseData) {
                Exercise::firstOrCreate([
                    'lesson_id' => $lesson->id,
                    'prompt' => $exerciseData['prompt'],
                ], [
                    'type' => $exerciseData['type']->value,
                    'metadata' => $exerciseData['metadata'],
                ]);
            }
        }
    }
}
