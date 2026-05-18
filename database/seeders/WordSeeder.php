<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Word;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $englishWords = [
            ['word' => 'hello', 'meaning' => 'Used as a greeting', 'example_sentence' => 'Hello, how are you?'],
            ['word' => 'goodbye', 'meaning' => 'Used when leaving', 'example_sentence' => 'Goodbye, see you tomorrow!'],
            ['word' => 'thank you', 'meaning' => 'Expression of gratitude', 'example_sentence' => 'Thank you for your help.'],
            ['word' => 'please', 'meaning' => 'Used to make a polite request', 'example_sentence' => 'Please pass the salt.'],
            ['word' => 'water', 'meaning' => 'A clear liquid essential for life', 'example_sentence' => 'Can I have a glass of water?'],
            ['word' => 'food', 'meaning' => 'Any nutritious substance that people eat', 'example_sentence' => 'I need to buy some food.'],
            ['word' => 'house', 'meaning' => 'A building for human habitation', 'example_sentence' => 'They live in a big house.'],
            ['word' => 'book', 'meaning' => 'A written or printed work', 'example_sentence' => 'I am reading an interesting book.'],
            ['word' => 'friend', 'meaning' => 'A person you know well and like', 'example_sentence' => 'She is my best friend.'],
            ['word' => 'family', 'meaning' => 'A group of related people', 'example_sentence' => 'My family is very important to me.'],
        ];

        $spanishWords = [
            ['word' => 'hola', 'meaning' => 'Saludo informal', 'example_sentence' => 'Hola, ¿cómo estás?'],
            ['word' => 'adiós', 'meaning' => 'Despedida', 'example_sentence' => 'Adiós, ¡nos vemos mañana!'],
            ['word' => 'gracias', 'meaning' => 'Expresión de gratitud', 'example_sentence' => 'Gracias por tu ayuda.'],
            ['word' => 'por favor', 'meaning' => 'Para hacer una petición cortés', 'example_sentence' => 'Por favor, pásame la sal.'],
            ['word' => 'agua', 'meaning' => 'Líquido esencial para la vida', 'example_sentence' => '¿Puedo tener un vaso de agua?'],
            ['word' => 'comida', 'meaning' => 'Sustancia nutritiva que se consume', 'example_sentence' => 'Necesito comprar comida.'],
            ['word' => 'casa', 'meaning' => 'Edificio para vivienda', 'example_sentence' => 'Viven en una casa grande.'],
            ['word' => 'libro', 'meaning' => 'Obra escrita o impresa', 'example_sentence' => 'Estoy leyendo un libro interesante.'],
            ['word' => 'amigo', 'meaning' => 'Persona que se conoce bien y se aprecia', 'example_sentence' => 'Ella es mi mejor amiga.'],
            ['word' => 'familia', 'meaning' => 'Grupo de personas relacionadas', 'example_sentence' => 'Mi familia es muy importante para mí.'],
        ];

        $english = Language::where('code', 'en')->first();
        $spanish = Language::where('code', 'es')->first();

        if ($english) {
            foreach ($englishWords as $wordData) {
                Word::firstOrCreate(
                    ['language_id' => $english->id, 'word' => $wordData['word']],
                    $wordData
                );
            }
        }

        if ($spanish) {
            foreach ($spanishWords as $wordData) {
                Word::firstOrCreate(
                    ['language_id' => $spanish->id, 'word' => $wordData['word']],
                    $wordData
                );
            }
        }
    }
}
