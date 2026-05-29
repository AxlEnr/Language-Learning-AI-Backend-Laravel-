<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Word;
use Illuminate\Database\Seeder;

class EnglishVocabularySeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();

        if (!$english) {
            return;
        }

        $words = array_merge(
            $this->greetingsAndPoliteness(),
            $this->familyAndPeople(),
            $this->numbersAndTime(),
            $this->foodAndDrink(),
            $this->bodyAndHealth(),
            $this->clothingAndShopping(),
            $this->homeAndFurniture(),
            $this->travelAndDirections(),
            $this->workAndBusiness(),
            $this->educationAndStudy(),
            $this->natureAndWeather(),
            $this->emotionsAndPersonality(),
            $this->technology(),
            $this->commonAdjectives(),
            $this->commonVerbs(),
            $this->phrasalVerbs(),
            $this->idioms(),
            $this->academicVocabulary(),
            $this->professionalVocabulary(),
        );

        foreach ($words as $wordData) {
            Word::firstOrCreate(
                ['language_id' => $english->id, 'word' => $wordData['word']],
                $wordData
            );
        }
    }

    private function greetingsAndPoliteness(): array
    {
        return [
            ['word' => 'hello', 'meaning' => 'Used as a greeting or to begin a conversation', 'example_sentence' => 'Hello, how may I help you today?'],
            ['word' => 'goodbye', 'meaning' => 'Used to express farewell when leaving', 'example_sentence' => 'Goodbye, it was nice meeting you.'],
            ['word' => 'please', 'meaning' => 'Used to make a polite request or express preference', 'example_sentence' => 'Could you please pass the salt?'],
            ['word' => 'thank you', 'meaning' => 'An expression of gratitude and appreciation', 'example_sentence' => 'Thank you for helping me with this project.'],
            ['word' => 'sorry', 'meaning' => 'Used to express apology, regret, or sympathy', 'example_sentence' => "I'm sorry for being late to the meeting."],
            ['word' => 'excuse me', 'meaning' => 'Used to get attention, apologize, or ask to pass', 'example_sentence' => 'Excuse me, could you tell me where the station is?'],
            ['word' => 'good morning', 'meaning' => 'A greeting used in the morning', 'example_sentence' => 'Good morning, everyone! Let\'s start today\'s lesson.'],
            ['word' => 'good evening', 'meaning' => 'A greeting used after late afternoon', 'example_sentence' => 'Good evening, welcome to the restaurant.'],
            ['word' => 'welcome', 'meaning' => 'Used to greet someone who has arrived', 'example_sentence' => 'Welcome to our country! We hope you enjoy your stay.'],
            ['word' => 'cheers', 'meaning' => 'Used to express good wishes before drinking; also informally for thanks or goodbye', 'example_sentence' => 'Cheers! Let\'s celebrate the new project.'],
        ];
    }

    private function familyAndPeople(): array
    {
        return [
            ['word' => 'mother', 'meaning' => 'A female parent', 'example_sentence' => 'My mother works as a teacher at the local school.'],
            ['word' => 'father', 'meaning' => 'A male parent', 'example_sentence' => 'My father taught me how to ride a bicycle.'],
            ['word' => 'sister', 'meaning' => 'A female sibling', 'example_sentence' => 'My sister is two years older than me.'],
            ['word' => 'brother', 'meaning' => 'A male sibling', 'example_sentence' => 'My brother and I share the same room.'],
            ['word' => 'grandmother', 'meaning' => 'The mother of one\'s parent', 'example_sentence' => 'My grandmother makes the best chocolate cake.'],
            ['word' => 'grandfather', 'meaning' => 'The father of one\'s parent', 'example_sentence' => 'My grandfather always tells us interesting stories.'],
            ['word' => 'uncle', 'meaning' => 'The brother of one\'s parent or the husband of one\'s aunt', 'example_sentence' => 'My uncle lives in London and visits every summer.'],
            ['word' => 'aunt', 'meaning' => 'The sister of one\'s parent or the wife of one\'s uncle', 'example_sentence' => 'My aunt is an excellent cook.'],
            ['word' => 'cousin', 'meaning' => 'The child of one\'s aunt or uncle', 'example_sentence' => 'I have three cousins on my mother\'s side.'],
            ['word' => 'neighbor', 'meaning' => 'A person living next to or near another', 'example_sentence' => 'Our neighbors are very friendly and helpful.'],
            ['word' => 'colleague', 'meaning' => 'A person with whom one works', 'example_sentence' => 'My colleague helped me finish the report on time.'],
            ['word' => 'friend', 'meaning' => 'A person with whom one has a bond of mutual affection', 'example_sentence' => 'She is my best friend; we\'ve known each other since childhood.'],
            ['word' => 'stranger', 'meaning' => 'A person one does not know', 'example_sentence' => 'A kind stranger helped me find my way in the city.'],
        ];
    }

    private function numbersAndTime(): array
    {
        return [
            ['word' => 'dozen', 'meaning' => 'A group of twelve', 'example_sentence' => 'I bought a dozen eggs from the store.'],
            ['word' => 'hundred', 'meaning' => 'The number 100', 'example_sentence' => 'There were about a hundred people at the event.'],
            ['word' => 'thousand', 'meaning' => 'The number 1,000', 'example_sentence' => 'The stadium holds five thousand spectators.'],
            ['word' => 'million', 'meaning' => 'The number 1,000,000', 'example_sentence' => 'Millions of people visit this museum every year.'],
            ['word' => 'morning', 'meaning' => 'The period between midnight and noon', 'example_sentence' => 'I usually exercise in the morning before work.'],
            ['word' => 'afternoon', 'meaning' => 'The period between noon and evening', 'example_sentence' => 'Let\'s meet for coffee tomorrow afternoon.'],
            ['word' => 'evening', 'meaning' => 'The period between late afternoon and night', 'example_sentence' => 'We had a lovely evening walk along the river.'],
            ['word' => 'midnight', 'meaning' => 'Twelve o\'clock at night', 'example_sentence' => 'The fireworks started at midnight on New Year\'s Eve.'],
            ['word' => 'dawn', 'meaning' => 'The first appearance of light in the sky before sunrise', 'example_sentence' => 'We woke at dawn to watch the sunrise over the mountains.'],
            ['word' => 'century', 'meaning' => 'A period of one hundred years', 'example_sentence' => 'This building was constructed in the last century.'],
            ['word' => 'decade', 'meaning' => 'A period of ten years', 'example_sentence' => 'Technology has changed dramatically over the past decade.'],
            ['word' => 'schedule', 'meaning' => 'A plan for carrying out activities in a set time', 'example_sentence' => 'My schedule is very busy this week with meetings.'],
            ['word' => 'quarter', 'meaning' => 'One fourth; fifteen minutes before or after the hour', 'example_sentence' => 'The meeting starts at a quarter past three.'],
        ];
    }

    private function foodAndDrink(): array
    {
        return [
            ['word' => 'breakfast', 'meaning' => 'The first meal of the day', 'example_sentence' => 'I always have toast and coffee for breakfast.'],
            ['word' => 'lunch', 'meaning' => 'A meal eaten in the middle of the day', 'example_sentence' => 'Let\'s grab lunch together at the new restaurant.'],
            ['word' => 'dinner', 'meaning' => 'The main meal of the day, usually in the evening', 'example_sentence' => 'We\'re having roast chicken for dinner tonight.'],
            ['word' => 'appetizer', 'meaning' => 'A small dish served before the main course', 'example_sentence' => 'The bruschetta was an excellent appetizer.'],
            ['word' => 'beverage', 'meaning' => 'A drink other than water', 'example_sentence' => 'Hot beverages are available at the coffee shop.'],
            ['word' => 'recipe', 'meaning' => 'A set of instructions for preparing a dish', 'example_sentence' => 'My grandmother gave me her secret recipe for pasta sauce.'],
            ['word' => 'ingredient', 'meaning' => 'A component part of a mixture or recipe', 'example_sentence' => 'Fresh ingredients make all the difference in cooking.'],
            ['word' => 'delicious', 'meaning' => 'Highly pleasing to the taste', 'example_sentence' => 'The chocolate cake was absolutely delicious.'],
            ['word' => 'portion', 'meaning' => 'An amount of food served to one person', 'example_sentence' => 'The portions at this restaurant are very generous.'],
            ['word' => 'vegetarian', 'meaning' => 'A person who does not eat meat', 'example_sentence' => 'The restaurant has many vegetarian options on the menu.'],
            ['word' => 'allergy', 'meaning' => 'A damaging immune response to a substance', 'example_sentence' => 'I have a nut allergy, so I need to be careful.'],
            ['word' => 'spicy', 'meaning' => 'Flavored with or fragrant with spice', 'example_sentence' => 'The curry was too spicy for me to eat.'],
        ];
    }

    private function bodyAndHealth(): array
    {
        return [
            ['word' => 'headache', 'meaning' => 'A pain in the head', 'example_sentence' => 'I have a terrible headache after working on the computer all day.'],
            ['word' => 'fever', 'meaning' => 'An abnormally high body temperature', 'example_sentence' => 'The child has a high fever and needs to see a doctor.'],
            ['word' => 'prescription', 'meaning' => 'A written order from a doctor for medicine', 'example_sentence' => 'The doctor gave me a prescription for antibiotics.'],
            ['word' => 'appointment', 'meaning' => 'An arrangement to meet someone at a particular time', 'example_sentence' => 'I have a doctor\'s appointment next Monday at 10 AM.'],
            ['word' => 'symptom', 'meaning' => 'A physical feature indicating a condition', 'example_sentence' => 'A sore throat is a common symptom of a cold.'],
            ['word' => 'exercise', 'meaning' => 'Physical activity to stay healthy', 'example_sentence' => 'Regular exercise helps reduce stress.'],
            ['word' => 'exhausted', 'meaning' => 'Completely worn out or tired', 'example_sentence' => 'After the marathon, I was completely exhausted.'],
            ['word' => 'brace', 'meaning' => 'A device that holds something in position or supports a weak body part', 'example_sentence' => 'She wore a brace on her knee after the injury.'],
            ['word' => 'pharmacy', 'meaning' => 'A store where medicines are sold', 'example_sentence' => 'You can pick up your medication at the pharmacy on Main Street.'],
            ['word' => 'recover', 'meaning' => 'To return to a normal state of health', 'example_sentence' => 'It took her two weeks to recover from the flu.'],
        ];
    }

    private function clothingAndShopping(): array
    {
        return [
            ['word' => 'receipt', 'meaning' => 'A written acknowledgment of payment', 'example_sentence' => 'Would you like a receipt with your purchase?'],
            ['word' => 'discount', 'meaning' => 'A deduction from the usual cost', 'example_sentence' => 'There\'s a 20 percent discount on all winter coats.'],
            ['word' => 'size', 'meaning' => 'The dimensions of something; clothing measurement', 'example_sentence' => 'Do you have this dress in a smaller size?'],
            ['word' => 'fitting room', 'meaning' => 'A room in a store where customers try on clothes', 'example_sentence' => 'The fitting room is at the back of the store.'],
            ['word' => 'affordable', 'meaning' => 'Inexpensively priced; within one\'s budget', 'example_sentence' => 'These shoes are really affordable and good quality.'],
            ['word' => 'expensive', 'meaning' => 'Costing a lot of money', 'example_sentence' => 'The hotel was quite expensive, but it was worth it.'],
            ['word' => 'exchange', 'meaning' => 'To give one thing and receive another in return', 'example_sentence' => 'I need to exchange this shirt for a larger size.'],
            ['word' => 'refund', 'meaning' => 'Money returned to a customer', 'example_sentence' => 'I\'d like a refund because the product was defective.'],
            ['word' => 'bargain', 'meaning' => 'Something offered at a good price; a deal', 'example_sentence' => 'This jacket was a real bargain at half price.'],
            ['word' => 'wallet', 'meaning' => 'A small folding case for carrying money and cards', 'example_sentence' => 'I left my wallet at home this morning.'],
        ];
    }

    private function homeAndFurniture(): array
    {
        return [
            ['word' => 'kitchen', 'meaning' => 'A room where food is prepared and cooked', 'example_sentence' => 'The kitchen has all new appliances.'],
            ['word' => 'bedroom', 'meaning' => 'A room for sleeping', 'example_sentence' => 'The bedroom overlooks the garden.'],
            ['word' => 'bathroom', 'meaning' => 'A room containing a toilet and sink, often a bathtub', 'example_sentence' => 'The house has two bathrooms.'],
            ['word' => 'furniture', 'meaning' => 'Large movable equipment used to make a space suitable for living', 'example_sentence' => 'We need to buy new furniture for the living room.'],
            ['word' => 'appliance', 'meaning' => 'A device or machine used for a particular household task', 'example_sentence' => 'The kitchen appliance is making a strange noise.'],
            ['word' => 'tenant', 'meaning' => 'A person who occupies land or property rented from a landlord', 'example_sentence' => 'The tenant signed a one-year lease for the apartment.'],
            ['word' => 'landlord', 'meaning' => 'A person who rents out property', 'example_sentence' => 'The landlord is responsible for fixing the plumbing.'],
            ['word' => 'lease', 'meaning' => 'A contract for renting property', 'example_sentence' => 'I signed a two-year lease for the apartment.'],
            ['word' => 'neighborhood', 'meaning' => 'The area surrounding a particular place or person', 'example_sentence' => 'This is a quiet neighborhood with a lot of families.'],
            ['word' => 'suburb', 'meaning' => 'An outlying residential area of a city', 'example_sentence' => 'They moved to the suburb for better schools.'],
        ];
    }

    private function travelAndDirections(): array
    {
        return [
            ['word' => 'destination', 'meaning' => 'The place to which someone is going', 'example_sentence' => 'What is your final destination for this trip?'],
            ['word' => 'itinerary', 'meaning' => 'A planned route or journey', 'example_sentence' => 'Here is the itinerary for our trip to Europe.'],
            ['word' => 'luggage', 'meaning' => 'Suitcases or bags for traveling', 'example_sentence' => 'Please keep your luggage with you at all times.'],
            ['word' => 'boarding pass', 'meaning' => 'A document permitting a passenger to board an aircraft', 'example_sentence' => 'Please have your boarding pass ready at the gate.'],
            ['word' => 'departure', 'meaning' => 'The action of leaving, especially by plane or train', 'example_sentence' => 'The departure time has been moved to 3 PM.'],
            ['word' => 'arrival', 'meaning' => 'The action of reaching a destination', 'example_sentence' => 'Our estimated arrival time is 6 PM.'],
            ['word' => 'intersection', 'meaning' => 'A point where two roads cross', 'example_sentence' => 'Turn left at the next intersection.'],
            ['word' => 'highway', 'meaning' => 'A main road connecting cities', 'example_sentence' => 'Take the highway to get there faster.'],
            ['word' => 'compass', 'meaning' => 'An instrument showing direction', 'example_sentence' => 'A compass always points north.'],
            ['word' => 'landmark', 'meaning' => 'A recognizable feature used for navigation', 'example_sentence' => 'The old clock tower is a famous landmark in the city.'],
            ['word' => 'accommodation', 'meaning' => 'A place to live or stay', 'example_sentence' => 'We booked accommodation near the beach.'],
            ['word' => 'currency', 'meaning' => 'The system of money used in a country', 'example_sentence' => 'What currency do they use in Japan?'],
            ['word' => 'customs', 'meaning' => 'The officials who inspect goods entering a country', 'example_sentence' => 'We had to declare our purchases at customs.'],
        ];
    }

    private function workAndBusiness(): array
    {
        return [
            ['word' => 'deadline', 'meaning' => 'The latest time by which something must be completed', 'example_sentence' => 'The deadline for submitting the report is Friday.'],
            ['word' => 'meeting', 'meaning' => 'An assembly of people for discussion', 'example_sentence' => 'We have a team meeting scheduled for this afternoon.'],
            ['word' => 'colleague', 'meaning' => 'A person with whom one works', 'example_sentence' => 'My colleague and I are working on the same project.'],
            ['word' => 'salary', 'meaning' => 'Regular payment for employment', 'example_sentence' => 'They offered a competitive salary for the position.'],
            ['word' => 'promotion', 'meaning' => 'Advancement in rank or position', 'example_sentence' => 'She received a promotion after three years of hard work.'],
            ['word' => 'resume', 'meaning' => 'A document describing one\'s qualifications and experience', 'example_sentence' => 'I updated my resume before applying for the job.'],
            ['word' => 'interview', 'meaning' => 'A meeting to assess a candidate\'s suitability', 'example_sentence' => 'The job interview is next Wednesday at 2 PM.'],
            ['word' => 'project', 'meaning' => 'An individual or collaborative enterprise planned to achieve a goal', 'example_sentence' => 'The project is expected to be completed by December.'],
            ['word' => 'strategy', 'meaning' => 'A plan of action designed to achieve a major goal', 'example_sentence' => 'The company needs a new marketing strategy.'],
            ['word' => 'client', 'meaning' => 'A person who uses the services of a professional or business', 'example_sentence' => 'We have an important meeting with a new client tomorrow.'],
            ['word' => 'feedback', 'meaning' => 'Information about reactions to a product or performance', 'example_sentence' => 'The manager gave constructive feedback on my presentation.'],
            ['word' => 'negotiate', 'meaning' => 'To discuss terms to reach an agreement', 'example_sentence' => 'We need to negotiate a better price with the supplier.'],
            ['word' => 'budget', 'meaning' => 'An estimate of income and spending for a set period', 'example_sentence' => 'The marketing budget for this quarter has been approved.'],
        ];
    }

    private function educationAndStudy(): array
    {
        return [
            ['word' => 'lecture', 'meaning' => 'An educational talk to an audience', 'example_sentence' => 'The professor\'s lecture on quantum physics was fascinating.'],
            ['word' => 'assignment', 'meaning' => 'A task or piece of work allocated to someone', 'example_sentence' => 'The assignment is due by the end of this week.'],
            ['word' => 'research', 'meaning' => 'Systematic investigation to establish facts', 'example_sentence' => 'She is conducting research on climate change.'],
            ['word' => 'graduation', 'meaning' => 'The receiving of a diploma or degree', 'example_sentence' => 'The graduation ceremony will be held in the auditorium.'],
            ['word' => 'scholarship', 'meaning' => 'Financial aid awarded to a student', 'example_sentence' => 'She received a full scholarship to study engineering.'],
            ['word' => 'curriculum', 'meaning' => 'The subjects taught in a school or course', 'example_sentence' => 'The school has updated its science curriculum.'],
            ['word' => 'semester', 'meaning' => 'A half-year term in an academic institution', 'example_sentence' => 'The fall semester starts in September.'],
            ['word' => 'thesis', 'meaning' => 'A long essay involving personal research for a university degree', 'example_sentence' => 'He is writing his thesis on renewable energy sources.'],
            ['word' => 'plagiarism', 'meaning' => 'Using someone else\'s work without giving credit', 'example_sentence' => 'Plagiarism is a serious academic offense.'],
            ['word' => 'library', 'meaning' => 'A building or room containing books for use or borrowing', 'example_sentence' => 'I spend most of my afternoons studying in the library.'],
        ];
    }

    private function natureAndWeather(): array
    {
        return [
            ['word' => 'breeze', 'meaning' => 'A gentle wind', 'example_sentence' => 'A cool breeze made the evening walk pleasant.'],
            ['word' => 'thunderstorm', 'meaning' => 'A storm with thunder and lightning', 'example_sentence' => 'A thunderstorm is expected this afternoon.'],
            ['word' => 'fog', 'meaning' => 'A thick cloud of tiny water droplets near the ground', 'example_sentence' => 'The fog was so thick we could barely see the road.'],
            ['word' => 'humid', 'meaning' => 'Containing a high amount of water vapor in the air', 'example_sentence' => 'The weather today is very humid and uncomfortable.'],
            ['word' => 'landscape', 'meaning' => 'The visible features of an area of land', 'example_sentence' => 'The landscape of the valley is breathtaking in autumn.'],
            ['word' => 'wilderness', 'meaning' => 'An uncultivated, uninhabited natural area', 'example_sentence' => 'They hiked through the wilderness for three days.'],
            ['word' => 'shore', 'meaning' => 'The land along the edge of a body of water', 'example_sentence' => 'We walked along the shore collecting seashells.'],
            ['word' => 'glacier', 'meaning' => 'A slowly moving mass of ice', 'example_sentence' => 'The glacier has been retreating due to climate change.'],
            ['word' => 'earthquake', 'meaning' => 'A sudden shaking of the ground', 'example_sentence' => 'The earthquake measured 5.2 on the Richter scale.'],
            ['word' => 'drought', 'meaning' => 'A prolonged period of abnormally low rainfall', 'example_sentence' => 'The region is experiencing its worst drought in decades.'],
        ];
    }

    private function emotionsAndPersonality(): array
    {
        return [
            ['word' => 'anxious', 'meaning' => 'Experiencing worry or unease', 'example_sentence' => 'She felt anxious before the important exam.'],
            ['word' => 'grateful', 'meaning' => 'Feeling or showing appreciation', 'example_sentence' => 'I\'m truly grateful for all your support.'],
            ['word' => 'confident', 'meaning' => 'Feeling sure about one\'s abilities', 'example_sentence' => 'He is confident that he will pass the interview.'],
            ['word' => 'curious', 'meaning' => 'Eager to know or learn something', 'example_sentence' => 'Children are naturally curious about the world.'],
            ['word' => 'generous', 'meaning' => 'Willing to give more than is expected', 'example_sentence' => 'She is very generous and always helps those in need.'],
            ['word' => 'stubborn', 'meaning' => 'Having a determination not to change one\'s mind', 'example_sentence' => 'He is too stubborn to admit he was wrong.'],
            ['word' => 'reliable', 'meaning' => 'Consistent in quality; able to be trusted', 'example_sentence' => 'She is a reliable employee who always meets deadlines.'],
            ['word' => 'sympathetic', 'meaning' => 'Feeling or showing concern for others', 'example_sentence' => 'My boss was very sympathetic when I was ill.'],
            ['word' => 'enthusiastic', 'meaning' => 'Having or showing intense excitement', 'example_sentence' => 'The students were enthusiastic about the field trip.'],
            ['word' => 'frustrated', 'meaning' => 'Feeling upset because of inability to change or achieve something', 'example_sentence' => 'I felt frustrated when the computer crashed again.'],
        ];
    }

    private function technology(): array
    {
        return [
            ['word' => 'software', 'meaning' => 'Programs and operating systems used by a computer', 'example_sentence' => 'We need to update the software before the presentation.'],
            ['word' => 'database', 'meaning' => 'An organized collection of structured information', 'example_sentence' => 'All customer information is stored in our database.'],
            ['word' => 'algorithm', 'meaning' => 'A set of rules for solving a problem in computing', 'example_sentence' => 'The search algorithm was updated to improve accuracy.'],
            ['word' => 'bandwidth', 'meaning' => 'The maximum rate of data transfer; also informally means capacity', 'example_sentence' => 'The video call requires a lot of bandwidth.'],
            ['word' => 'download', 'meaning' => 'To copy data from the internet to a computer', 'example_sentence' => 'You can download the app from our website.'],
            ['word' => 'platform', 'meaning' => 'A digital environment for running software or services', 'example_sentence' => 'Our course is available on multiple platforms.'],
            ['word' => 'backup', 'meaning' => 'An extra copy of data stored for safety', 'example_sentence' => 'Make sure you have a backup of all your important files.'],
            ['word' => 'password', 'meaning' => 'A secret word used for authentication', 'example_sentence' => 'Please change your password every three months.'],
            ['word' => 'encryption', 'meaning' => 'The process of converting data into a code to prevent unauthorized access', 'example_sentence' => 'End-to-end encryption protects your messages.'],
            ['word' => 'cloud', 'meaning' => 'Remote servers accessed over the internet', 'example_sentence' => 'All our files are stored in the cloud for easy access.'],
        ];
    }

    private function commonAdjectives(): array
    {
        return [
            ['word' => 'enormous', 'meaning' => 'Very large in size or quantity', 'example_sentence' => 'The elephant is an enormous animal.'],
            ['word' => 'tiny', 'meaning' => 'Very small in size', 'example_sentence' => 'The tiny kitten fit in the palm of my hand.'],
            ['word' => 'brilliant', 'meaning' => 'Exceptionally clever or talented; very bright', 'example_sentence' => 'She had a brilliant idea for the science project.'],
            ['word' => 'genuine', 'meaning' => 'Authentic; truly what it is said to be', 'example_sentence' => 'His apology seemed genuine and heartfelt.'],
            ['word' => 'temporary', 'meaning' => 'Lasting for only a limited time', 'example_sentence' => 'This is a temporary solution until we find a permanent fix.'],
            ['word' => 'permanent', 'meaning' => 'Lasting or remaining unchanged indefinitely', 'example_sentence' => 'She accepted a permanent position at the company.'],
            ['word' => 'obvious', 'meaning' => 'Easily perceived or understood; clear', 'example_sentence' => 'It was obvious that he hadn\'t studied for the test.'],
            ['word' => 'significant', 'meaning' => 'Sufficiently great to be noteworthy', 'example_sentence' => 'There has been a significant improvement in her grades.'],
            ['word' => 'ancient', 'meaning' => 'Belonging to the very distant past', 'example_sentence' => 'The ancient ruins attract thousands of tourists each year.'],
            ['word' => 'contemporary', 'meaning' => 'Living or occurring at the same time; modern', 'example_sentence' => 'The museum features both ancient and contemporary art.'],
            ['word' => 'crucial', 'meaning' => 'Of great importance; decisive', 'example_sentence' => 'Time management is crucial for exam success.'],
            ['word' => 'frequent', 'meaning' => 'Occurring often; happening at short intervals', 'example_sentence' => 'Frequent practice is essential for learning a language.'],
        ];
    }

    private function commonVerbs(): array
    {
        return [
            ['word' => 'achieve', 'meaning' => 'To successfully bring about or reach a desired objective', 'example_sentence' => 'She worked hard to achieve her dream of becoming a doctor.'],
            ['word' => 'determine', 'meaning' => 'To ascertain or decide something', 'example_sentence' => 'We need to determine the cause of the problem.'],
            ['word' => 'establish', 'meaning' => 'To set up on a firm or permanent basis', 'example_sentence' => 'The company was established in 1990.'],
            ['word' => 'persuade', 'meaning' => 'To convince someone to do or believe something', 'example_sentence' => 'He persuaded his friend to join the gym.'],
            ['word' => 'investigate', 'meaning' => 'To examine systematically', 'example_sentence' => 'The police are investigating the incident.'],
            ['word' => 'recognize', 'meaning' => 'To identify someone or something from previous experience', 'example_sentence' => 'I didn\'t recognize her with her new hairstyle.'],
            ['word' => 'improve', 'meaning' => 'To make or become better', 'example_sentence' => 'You can improve your English with daily practice.'],
            ['word' => 'develop', 'meaning' => 'To grow or cause to grow; to elaborate', 'example_sentence' => 'The team is developing a new product for the market.'],
            ['word' => 'contribute', 'meaning' => 'To give something to help achieve a result', 'example_sentence' => 'Everyone contributed ideas during the brainstorming session.'],
            ['word' => 'maintain', 'meaning' => 'To cause to continue; to keep in good condition', 'example_sentence' => 'Regular exercise helps maintain good health.'],
            ['word' => 'require', 'meaning' => 'To need as essential; to demand', 'example_sentence' => 'This job requires excellent communication skills.'],
            ['word' => 'suggest', 'meaning' => 'To put forward for consideration', 'example_sentence' => 'I suggest we start the meeting with a brief review.'],
        ];
    }

    private function phrasalVerbs(): array
    {
        return [
            ['word' => 'look forward to', 'meaning' => 'To be excited about something that will happen', 'example_sentence' => 'I look forward to seeing you at the conference next week.'],
            ['word' => 'give up', 'meaning' => 'To stop trying; to quit', 'example_sentence' => 'Don\'t give up on your dreams, no matter how hard it gets.'],
            ['word' => 'carry out', 'meaning' => 'To perform or complete a task', 'example_sentence' => 'The team carried out the experiment according to plan.'],
            ['word' => 'figure out', 'meaning' => 'To understand or solve something', 'example_sentence' => 'I finally figured out how to use the new software.'],
            ['word' => 'turn out', 'meaning' => 'To happen in a particular way or to have a result', 'example_sentence' => 'The event turned out to be a great success.'],
            ['word' => 'bring up', 'meaning' => 'To mention or introduce a topic; to raise a child', 'example_sentence' => 'She brought up an interesting point during the discussion.'],
            ['word' => 'come across', 'meaning' => 'To find or encounter by chance', 'example_sentence' => 'I came across an old photo album while cleaning the attic.'],
            ['word' => 'put off', 'meaning' => 'To postpone; to delay', 'example_sentence' => 'Don\'t put off until tomorrow what you can do today.'],
            ['word' => 'take over', 'meaning' => 'To gain control of or assume responsibility for', 'example_sentence' => 'She will take over as manager when John retires.'],
            ['word' => 'work out', 'meaning' => 'To exercise; to find a solution', 'example_sentence' => 'I\'m sure we can work out a solution to this problem.'],
            ['word' => 'look into', 'meaning' => 'To investigate or examine', 'example_sentence' => 'The manager promised to look into the complaint.'],
            ['word' => 'catch up', 'meaning' => 'To reach the same level or position as others', 'example_sentence' => 'I need to catch up on all the emails I missed while on vacation.'],
        ];
    }

    private function idioms(): array
    {
        return [
            ['word' => 'piece of cake', 'meaning' => 'Something very easy to do', 'example_sentence' => 'The exam was a piece of cake for her.'],
            ['word' => 'break a leg', 'meaning' => 'Good luck (especially before a performance)', 'example_sentence' => 'Break a leg in your audition tonight!'],
            ['word' => 'hit the nail on the head', 'meaning' => 'To describe exactly what is causing a situation or problem', 'example_sentence' => 'You hit the nail on the head with that analysis.'],
            ['word' => 'under the weather', 'meaning' => 'Feeling ill or sick', 'example_sentence' => "I'm feeling a bit under the weather today."],
            ['word' => 'once in a blue moon', 'meaning' => 'Very rarely', 'example_sentence' => 'She visits her hometown once in a blue moon.'],
            ['word' => 'cost an arm and a leg', 'meaning' => 'To be very expensive', 'example_sentence' => 'That luxury car must have cost an arm and a leg.'],
            ['word' => 'the ball is in your court', 'meaning' => 'It is your decision or responsibility now', 'example_sentence' => "I've made my offer. Now the ball is in your court."],
            ['word' => 'burn the midnight oil', 'meaning' => 'To work late into the night', 'example_sentence' => 'Students often burn the midnight oil before final exams.'],
            ['word' => 'let the cat out of the bag', 'meaning' => 'To reveal a secret accidentally', 'example_sentence' => 'She let the cat out of the bag about the surprise party.'],
            ['word' => 'on the same page', 'meaning' => 'Having a shared understanding of a situation', 'example_sentence' => 'Before we start, let\'s make sure we\'re all on the same page.'],
        ];
    }

    private function academicVocabulary(): array
    {
        return [
            ['word' => 'analyze', 'meaning' => 'To examine in detail for purposes of explanation', 'example_sentence' => 'We need to analyze the data before drawing conclusions.'],
            ['word' => 'hypothesis', 'meaning' => 'A proposed explanation based on limited evidence', 'example_sentence' => 'The scientist tested her hypothesis through experiments.'],
            ['word' => 'conclusion', 'meaning' => 'A judgment reached by reasoning', 'example_sentence' => 'In conclusion, the study supports the original theory.'],
            ['word' => 'evidence', 'meaning' => 'Facts indicating whether a belief is true or valid', 'example_sentence' => 'There is strong evidence that exercise improves mental health.'],
            ['word' => 'perspective', 'meaning' => 'A particular attitude toward something; a point of view', 'example_sentence' => 'Understanding different perspectives is important in sociology.'],
            ['word' => 'significant', 'meaning' => 'Large enough to be noticed or have an effect', 'example_sentence' => 'The study found a significant difference between the two groups.'],
            ['word' => 'implication', 'meaning' => 'A conclusion that can be drawn from something', 'example_sentence' => 'The implications of this research are far-reaching.'],
            ['word' => 'methodology', 'meaning' => 'The systematic methods used in a particular field', 'example_sentence' => 'The research methodology was explained in detail.'],
            ['word' => 'framework', 'meaning' => 'An underlying structure or plan', 'example_sentence' => 'We need a theoretical framework to guide our analysis.'],
            ['word' => 'synthesize', 'meaning' => 'To combine separate elements into a whole', 'example_sentence' => 'The essay synthesizes ideas from multiple sources.'],
        ];
    }

    private function professionalVocabulary(): array
    {
        return [
            ['word' => 'stakeholder', 'meaning' => 'A person with an interest or concern in something', 'example_sentence' => 'All stakeholders were invited to the planning meeting.'],
            ['word' => 'collaborate', 'meaning' => 'To work jointly on an activity or project', 'example_sentence' => 'The two departments will collaborate on the new initiative.'],
            ['word' => 'consensus', 'meaning' => 'General agreement among a group', 'example_sentence' => 'After much discussion, the team reached a consensus.'],
            ['word' => 'benchmark', 'meaning' => 'A standard against which things may be compared', 'example_sentence' => 'This report sets the benchmark for future evaluations.'],
            ['word' => 'streamline', 'meaning' => 'To make an organization or process more efficient', 'example_sentence' => 'We need to streamline our workflow to reduce costs.'],
            ['word' => 'implement', 'meaning' => 'To put a decision or plan into effect', 'example_sentence' => 'The company will implement the new policy starting next month.'],
            ['word' => 'leverage', 'meaning' => 'To use something to maximum advantage', 'example_sentence' => 'We should leverage our existing network to find new clients.'],
            ['word' => 'scalable', 'meaning' => 'Capable of being easily expanded or adapted', 'example_sentence' => 'We need a scalable solution that can grow with the business.'],
            ['word' => 'transparent', 'meaning' => 'Open to public scrutiny; having motives that are clear', 'example_sentence' => 'The company aims to be transparent about its environmental impact.'],
            ['word' => 'diversify', 'meaning' => 'To enlarge the range or variety of products, investments, etc.', 'example_sentence' => 'The company decided to diversify into new markets.'],
        ];
    }
}