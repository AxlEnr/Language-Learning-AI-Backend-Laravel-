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

class EnglishLessonsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $a1 = Level::where('code', 'A1')->first();
        $a2 = Level::where('code', 'A2')->first();
        $b1 = Level::where('code', 'B1')->first();
        $b2 = Level::where('code', 'B2')->first();

        if (!$english) {
            return;
        }

        $modulesData = $this->getA1Modules($a1);
        if ($a2) {
            $modulesData = array_merge($modulesData, $this->getA2Modules($a2));
        }
        if ($b1) {
            $modulesData = array_merge($modulesData, $this->getB1Modules($b1));
        }
        if ($b2) {
            $modulesData = array_merge($modulesData, $this->getB2Modules($b2));
        }

        foreach ($modulesData as $moduleData) {
            $lessonsData = $moduleData['lessons'];
            unset($moduleData['lessons']);

            $module = Module::firstOrCreate([
                'language_id' => $english->id,
                'level_id' => $moduleData['level_id'],
                'order_index' => $moduleData['order_index'],
            ], [
                'title' => $moduleData['title'],
                'description' => $moduleData['description'],
            ]);

            foreach ($lessonsData as $lessonData) {
                $exercisesData = $lessonData['exercises'];
                unset($lessonData['exercises']);

                $lesson = Lesson::firstOrCreate([
                    'module_id' => $module->id,
                    'order_index' => $lessonData['order_index'],
                ], [
                    'title' => $lessonData['title'],
                    'type' => $lessonData['type']->value,
                ]);

                foreach ($exercisesData as $exerciseData) {
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

    private function getA1Modules(Level $a1): array
    {
        return [
            [
                'level_id' => $a1->id,
                'order_index' => 1,
                'title' => 'Getting Started',
                'description' => 'Learn the basics of English communication',
                'lessons' => [
                    [
                        'title' => 'Basic Greetings',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What does "Hello" mean?',
                                'metadata' => [
                                    'options' => ['Goodbye', 'Hi / Greeting', 'Please', 'Sorry'],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "Good morning" to Spanish',
                                'metadata' => [
                                    'source_text' => 'Good morning',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['buenos días', 'buenos dias'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "Nice to ___ you!"',
                                'metadata' => [
                                    'sentence' => 'Nice to ___ you!',
                                    'blank_word' => 'meet',
                                    'hints' => ['Used when meeting someone for the first time'],
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
                                'prompt' => 'Practice introducing yourself. Tell me your name, where you are from, and one hobby you enjoy.',
                                'metadata' => [
                                    'topic' => 'introductions',
                                    'difficulty' => 1,
                                    'expected_elements' => ['name', 'origin', 'hobby'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Common Verbs: To Be',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I ___ a student."',
                                'metadata' => [
                                    'sentence' => 'I ___ a student.',
                                    'blank_word' => 'am',
                                    'hints' => ['Verb "to be" - first person singular'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Which is correct? "She ___ a teacher."',
                                'metadata' => [
                                    'options' => ['are', 'am', 'is', 'be'],
                                    'correct_option' => 2,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "We ___ from Mexico."',
                                'metadata' => [
                                    'sentence' => 'We ___ from Mexico.',
                                    'blank_word' => 'are',
                                    'hints' => ['Verb "to be" - first person plural'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Numbers and Colors',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 4,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What color is the sky on a clear day?',
                                'metadata' => [
                                    'options' => ['Red', 'Blue', 'Green', 'Yellow'],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "three" to Spanish',
                                'metadata' => [
                                    'source_text' => 'three',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['tres'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "A traffic light has ___ colors."',
                                'metadata' => [
                                    'sentence' => 'A traffic light has ___ colors.',
                                    'blank_word' => 'three',
                                    'hints' => ['A number between two and four'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'level_id' => $a1->id,
                'order_index' => 2,
                'title' => 'Everyday Life',
                'description' => 'Learn vocabulary and grammar for daily situations',
                'lessons' => [
                    [
                        'title' => 'Family Members',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What do you call your mother\'s brother?',
                                'metadata' => [
                                    'options' => ['Cousin', 'Uncle', 'Nephew', 'Grandfather'],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "hermana" to English',
                                'metadata' => [
                                    'source_text' => 'hermana',
                                    'target_language' => 'en',
                                    'accepted_answers' => ['sister'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "My father\'s mother is my ___."',
                                'metadata' => [
                                    'sentence' => 'My father\'s mother is my ___.',
                                    'blank_word' => 'grandmother',
                                    'hints' => ['Your parent\'s mother'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'At the Restaurant',
                        'type' => LessonType::LISTENING,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'You are at a restaurant and the waiter asks "What would you like to order?" What is the polite way to respond?',
                                'metadata' => [
                                    'options' => [
                                        'Give me food',
                                        'I\'d like the chicken, please',
                                        'Food now',
                                        'Want chicken',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'You are at a restaurant. Practice ordering a meal politely, asking about the menu, and requesting the bill.',
                                'metadata' => [
                                    'topic' => 'restaurant_ordering',
                                    'difficulty' => 1,
                                    'expected_elements' => ['polite request', 'menu question', 'ordering'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Simple Present Tense',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Choose the correct sentence:',
                                'metadata' => [
                                    'options' => [
                                        'She go to school every day.',
                                        'She goes to school every day.',
                                        'She going to school every day.',
                                        'She gone to school every day.',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "They ___ coffee in the morning."',
                                'metadata' => [
                                    'sentence' => 'They ___ coffee in the morning.',
                                    'blank_word' => 'drink',
                                    'hints' => ['Simple present, third person plural'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "He ___ television every evening."',
                                'metadata' => [
                                    'sentence' => 'He ___ television every evening.',
                                    'blank_word' => 'watches',
                                    'hints' => ['Simple present, third person singular - add -es'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Telling the Time',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 4,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What time is "quarter past three"?',
                                'metadata' => [
                                    'options' => ['2:45', '3:15', '3:30', '3:00'],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "It is half past seven" to Spanish',
                                'metadata' => [
                                    'source_text' => 'It is half past seven',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['son las siete y media', 'es las siete y media'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "The train leaves ___ six o\'clock."',
                                'metadata' => [
                                    'sentence' => 'The train leaves ___ six o\'clock.',
                                    'blank_word' => 'at',
                                    'hints' => ['Preposition used with specific times'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getA2Modules(Level $a2): array
    {
        return [
            [
                'level_id' => $a2->id,
                'order_index' => 1,
                'title' => 'Daily Routines',
                'description' => 'Describe your daily activities and habits',
                'lessons' => [
                    [
                        'title' => 'Morning Routine',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Which word means "to make yourself clean by washing"?',
                                'metadata' => [
                                    'options' => ['to cook', 'to shower', 'to sleep', 'to drive'],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I ___ my teeth every morning."',
                                'metadata' => [
                                    'sentence' => 'I ___ my teeth every morning.',
                                    'blank_word' => 'brush',
                                    'hints' => ['What you do with a toothbrush'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "I wake up at 7 AM" to Spanish',
                                'metadata' => [
                                    'source_text' => 'I wake up at 7 AM',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['me despierto a las 7 de la mañana', 'me despierto a las 7 AM'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Present Continuous',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Choose the correct sentence:',
                                'metadata' => [
                                    'options' => [
                                        'She is cooking dinner right now.',
                                        'She cooks dinner right now.',
                                        'She cooking dinner right now.',
                                        'She has cooked dinner right now.',
                                    ],
                                    'correct_option' => 0,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "Look! The children ___ in the park."',
                                'metadata' => [
                                    'sentence' => 'Look! The children ___ in the park.',
                                    'blank_word' => 'are playing',
                                    'hints' => ['Present continuous - happening right now'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I ___ a book at the moment."',
                                'metadata' => [
                                    'sentence' => 'I ___ a book at the moment.',
                                    'blank_word' => 'am reading',
                                    'hints' => ['Present continuous of "read"'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'At the Grocery Store',
                        'type' => LessonType::LISTENING,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'If you want to ask the price of something, what do you say?',
                                'metadata' => [
                                    'options' => [
                                        'How many is this?',
                                        'How much is this?',
                                        'What cost this?',
                                        'What price has this?',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'You are at a grocery store. Practice asking about prices, requesting items, and paying for your groceries.',
                                'metadata' => [
                                    'topic' => 'grocery_shopping',
                                    'difficulty' => 2,
                                    'expected_elements' => ['asking price', 'requesting item', 'paying'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Talking About Hobbies',
                        'type' => LessonType::SPEAKING,
                        'order_index' => 4,
                        'exercises' => [
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Talk about your hobbies and free time activities. What do you enjoy doing? How often do you do it?',
                                'metadata' => [
                                    'topic' => 'hobbies',
                                    'difficulty' => 2,
                                    'expected_elements' => ['hobby name', 'frequency', 'reason for enjoyment'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'level_id' => $a2->id,
                'order_index' => 2,
                'title' => 'Past and Future',
                'description' => 'Learn to talk about past events and future plans',
                'lessons' => [
                    [
                        'title' => 'Simple Past: Regular Verbs',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What is the past tense of "walk"?',
                                'metadata' => [
                                    'options' => ['walked', 'walked', 'walken', 'walks'],
                                    'correct_option' => 0,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "Yesterday, I ___ to the store."',
                                'metadata' => [
                                    'sentence' => 'Yesterday, I ___ to the store.',
                                    'blank_word' => 'walked',
                                    'hints' => ['Past tense of "walk"'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "She ___ a delicious cake last weekend."',
                                'metadata' => [
                                    'sentence' => 'She ___ a delicious cake last weekend.',
                                    'blank_word' => 'baked',
                                    'hints' => ['Past tense of "bake"'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Simple Past: Irregular Verbs',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What is the past tense of "go"?',
                                'metadata' => [
                                    'options' => ['goed', 'gone', 'went', 'going'],
                                    'correct_option' => 2,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I ___ a great movie last night."',
                                'metadata' => [
                                    'sentence' => 'I ___ a great movie last night.',
                                    'blank_word' => 'saw',
                                    'hints' => ['Past tense of "see"'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "I ate breakfast at 8 AM" to Spanish',
                                'metadata' => [
                                    'source_text' => 'I ate breakfast at 8 AM',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['desayuné a las 8 de la mañana', 'yo desayuné a las 8 AM'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Future with "Going To"',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Choose the correct sentence:',
                                'metadata' => [
                                    'options' => [
                                        'I going to visit my grandmother tomorrow.',
                                        'I am going to visit my grandmother tomorrow.',
                                        'I will going to visit my grandmother tomorrow.',
                                        'I am go visit my grandmother tomorrow.',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "We ___ going to have a picnic this Sunday."',
                                'metadata' => [
                                    'sentence' => 'We ___ going to have a picnic this Sunday.',
                                    'blank_word' => 'are',
                                    'hints' => ['Subject "We" uses "are" with going to'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Tell me about your plans for next weekend. Use "going to" to express future intentions.',
                                'metadata' => [
                                    'topic' => 'future_plans',
                                    'difficulty' => 2,
                                    'expected_elements' => ['going to', 'weekend plans', 'time expression'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getB1Modules(Level $b1): array
    {
        return [
            [
                'level_id' => $b1->id,
                'order_index' => 1,
                'title' => 'Travel and Tourism',
                'description' => 'Communicate effectively while traveling abroad',
                'lessons' => [
                    [
                        'title' => 'At the Airport',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What document do you need to board an international flight?',
                                'metadata' => [
                                    'options' => ['Driver\'s license', 'Passport', 'Library card', 'Credit card'],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "Please have your boarding pass and ___ ready for inspection."',
                                'metadata' => [
                                    'sentence' => 'Please have your boarding pass and ___ ready for inspection.',
                                    'blank_word' => 'passport',
                                    'hints' => ['Official travel document'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'You are at an airport check-in desk. Practice checking in for your flight, asking about baggage allowance, and finding your gate.',
                                'metadata' => [
                                    'topic' => 'airport_checkin',
                                    'difficulty' => 3,
                                    'expected_elements' => ['check-in', 'baggage', 'gate'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Present Perfect',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Choose the correct sentence:',
                                'metadata' => [
                                    'options' => [
                                        'I have visit Paris twice.',
                                        'I have visited Paris twice.',
                                        'I have visiting Paris twice.',
                                        'I has visited Paris twice.',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "She ___ never ___ sushi before."',
                                'metadata' => [
                                    'sentence' => 'She ___ never ___ sushi before.',
                                    'blank_word' => 'has tried',
                                    'hints' => ['Present perfect with "she" - has + past participle'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "I have lived here for five years" to Spanish',
                                'metadata' => [
                                    'source_text' => 'I have lived here for five years',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['he vivido aquí por cinco años', 'he vivido aquí durante cinco años', 'llevo cinco años viviendo aquí'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Hotel Check-in',
                        'type' => LessonType::LISTENING,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'You are checking into a hotel. Practice making a reservation, asking about amenities, and reporting a problem with your room.',
                                'metadata' => [
                                    'topic' => 'hotel_checkin',
                                    'difficulty' => 3,
                                    'expected_elements' => ['reservation', 'amenities', 'room issue'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Describing Travel Experiences',
                        'type' => LessonType::SPEAKING,
                        'order_index' => 4,
                        'exercises' => [
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Describe a memorable trip you have taken. Where did you go? What did you do? Use the present perfect and simple past tenses.',
                                'metadata' => [
                                    'topic' => 'travel_experience',
                                    'difficulty' => 3,
                                    'expected_elements' => ['destination', 'activities', 'present perfect or past tense'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'level_id' => $b1->id,
                'order_index' => 2,
                'title' => 'Work and Career',
                'description' => 'Professional English for the workplace',
                'lessons' => [
                    [
                        'title' => 'Job Interview Vocabulary',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What does "deadline" mean?',
                                'metadata' => [
                                    'options' => [
                                        'A line at the end of a page',
                                        'The latest time by which something should be completed',
                                        'A type of headline in a newspaper',
                                        'A deadline is a person who is late',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I have a lot of ___ in customer service."',
                                'metadata' => [
                                    'sentence' => 'I have a lot of ___ in customer service.',
                                    'blank_word' => 'experience',
                                    'hints' => ['Knowledge or skill from doing something for a long time'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::TRANSLATION,
                                'prompt' => 'Translate "salary" to Spanish',
                                'metadata' => [
                                    'source_text' => 'salary',
                                    'target_language' => 'es',
                                    'accepted_answers' => ['salario', 'sueldo'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Conditional Sentences (First Conditional)',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Choose the correct first conditional sentence:',
                                'metadata' => [
                                    'options' => [
                                        'If it will rain, I stay home.',
                                        'If it rains, I will stay home.',
                                        'If it rained, I would stay home.',
                                        'If it rains, I stay home.',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "If I ___ the job, I will move to London."',
                                'metadata' => [
                                    'sentence' => 'If I ___ the job, I will move to London.',
                                    'blank_word' => 'get',
                                    'hints' => ['First conditional uses present simple in the if-clause'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Talk about possible future situations using first conditional. For example: What will you do if you get a promotion?',
                                'metadata' => [
                                    'topic' => 'first_conditional',
                                    'difficulty' => 3,
                                    'expected_elements' => ['if + present simple', 'will + base verb'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Writing Professional Emails',
                        'type' => LessonType::SPEAKING,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Practice writing a professional email. You need to request vacation days from your manager. Be polite and clear about dates.',
                                'metadata' => [
                                    'topic' => 'professional_email',
                                    'difficulty' => 3,
                                    'expected_elements' => ['polite greeting', 'clear request', 'specific dates'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getB2Modules(Level $b2): array
    {
        return [
            [
                'level_id' => $b2->id,
                'order_index' => 1,
                'title' => 'Expressing Opinions',
                'description' => 'Master the art of expressing and defending your viewpoints',
                'lessons' => [
                    [
                        'title' => 'Agreeing and Disagreeing',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Which phrase is used to politely disagree?',
                                'metadata' => [
                                    'options' => [
                                        'I completely agree with you.',
                                        'You\'re absolutely right.',
                                        'I see your point, but I think otherwise.',
                                        'That\'s exactly what I was thinking.',
                                    ],
                                    'correct_option' => 2,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I\'m afraid I have to ___ with you on that point."',
                                'metadata' => [
                                    'sentence' => 'I\'m afraid I have to ___ with you on that point.',
                                    'blank_word' => 'disagree',
                                    'hints' => ['Polite way to express opposition'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Discuss whether remote work is better than office work. Practice expressing your opinion, agreeing and disagreeing politely, and providing reasons.',
                                'metadata' => [
                                    'topic' => 'opinions_remote_work',
                                    'difficulty' => 4,
                                    'expected_elements' => ['opinion phrases', 'agreement/disagreement', 'supporting reasons'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Second and Third Conditionals',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'Which sentence is a second conditional?',
                                'metadata' => [
                                    'options' => [
                                        'If it rains, I will bring an umbrella.',
                                        'If I won the lottery, I would travel the world.',
                                        'If I had studied harder, I would have passed.',
                                        'If you heat water, it boils.',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete (second conditional): "If I ___ you, I would apologize."',
                                'metadata' => [
                                    'sentence' => 'If I ___ you, I would apologize.',
                                    'blank_word' => 'were',
                                    'hints' => ['Second conditional uses past simple in if-clause'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete (third conditional): "If she had left earlier, she ___ the train."',
                                'metadata' => [
                                    'sentence' => 'If she had left earlier, she ___ the train.',
                                    'blank_word' => 'would have caught',
                                    'hints' => ['Third conditional: would have + past participle'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Debating Current Issues',
                        'type' => LessonType::SPEAKING,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Debate the following statement: "Social media has done more harm than good to society." Present arguments for and against, then state your own position.',
                                'metadata' => [
                                    'topic' => 'social_media_debate',
                                    'difficulty' => 4,
                                    'expected_elements' => ['arguments for', 'arguments against', 'personal position', 'linking phrases'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'level_id' => $b2->id,
                'order_index' => 2,
                'title' => 'Advanced Communication',
                'description' => 'Nuanced language for sophisticated communication',
                'lessons' => [
                    [
                        'title' => 'Phrasal Verbs in Context',
                        'type' => LessonType::VOCABULARY,
                        'order_index' => 1,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => 'What does "look up to" mean?',
                                'metadata' => [
                                    'options' => [
                                        'To physically look upward',
                                        'To admire and respect someone',
                                        'To search for information',
                                        'To visit someone',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "I need to ___ up with a solution before the meeting."',
                                'metadata' => [
                                    'sentence' => 'I need to ___ up with a solution before the meeting.',
                                    'blank_word' => 'come',
                                    'hints' => ['Phrasal verb meaning "to think of or produce"'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "The meeting has been ___ off until next week."',
                                'metadata' => [
                                    'sentence' => 'The meeting has been ___ off until next week.',
                                    'blank_word' => 'put',
                                    'hints' => ['Phrasal verb meaning "to postpone"'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Reported Speech',
                        'type' => LessonType::GRAMMAR,
                        'order_index' => 2,
                        'exercises' => [
                            [
                                'type' => ExerciseType::MULTIPLE_CHOICE,
                                'prompt' => '"I am tired," she said. What is the correct reported speech?',
                                'metadata' => [
                                    'options' => [
                                        'She said she is tired.',
                                        'She said she was tired.',
                                        'She said she has been tired.',
                                        'She said she will be tired.',
                                    ],
                                    'correct_option' => 1,
                                ],
                            ],
                            [
                                'type' => ExerciseType::FILL_BLANK,
                                'prompt' => 'Complete: "He said he ___ finish the report by Friday."',
                                'metadata' => [
                                    'sentence' => 'He said he ___ finish the report by Friday.',
                                    'blank_word' => 'would',
                                    'hints' => ['Reported speech shifts "will" to this word'],
                                ],
                            ],
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Tell me about a conversation you had recently. Practice reporting what the other person said using reported speech (she said that..., he told me that..., they asked if...).',
                                'metadata' => [
                                    'topic' => 'reported_speech',
                                    'difficulty' => 4,
                                    'expected_elements' => ['reported statements', 'tense shift', 'reported questions'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Understanding Nuanced Arguments',
                        'type' => LessonType::LISTENING,
                        'order_index' => 3,
                        'exercises' => [
                            [
                                'type' => ExerciseType::AI_CHAT,
                                'prompt' => 'Listen to and discuss a complex topic. I will present an argument about whether artificial intelligence will replace human creativity. Respond with your perspective, using phrases like "While I acknowledge that...", "Nevertheless...", and "On the other hand...".',
                                'metadata' => [
                                    'topic' => 'ai_vs_creativity',
                                    'difficulty' => 4,
                                    'expected_elements' => ['concession phrases', 'contrast phrases', 'nuanced opinion'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}