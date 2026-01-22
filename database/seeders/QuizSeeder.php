<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a February quiz
        $quiz1 = Quiz::create([
            'name' => 'Quiz 1 - February',
            'start' => \Carbon\Carbon::create(2026, 2, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 2, 28, 17, 0, 0),
            'required_correct_answers' => 3,
        ]);

        // Add questions to quiz 1
        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'What is the capital of France?',
            'option_1' => 'London',
            'option_2' => 'Berlin',
            'option_3' => 'Paris',
            'option_4' => 'Madrid',
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Which planet is known as the Red Planet?',
            'option_1' => 'Venus',
            'option_2' => 'Mars',
            'option_3' => 'Jupiter',
            'option_4' => 'Saturn',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'What is 2 + 2?',
            'option_1' => '3',
            'option_2' => '4',
            'option_3' => '5',
            'option_4' => '6',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Who wrote "Romeo and Juliet"?',
            'option_1' => 'Charles Dickens',
            'option_2' => 'Jane Austen',
            'option_3' => 'William Shakespeare',
            'option_4' => 'Mark Twain',
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'What is the largest ocean on Earth?',
            'option_1' => 'Atlantic Ocean',
            'option_2' => 'Indian Ocean',
            'option_3' => 'Arctic Ocean',
            'option_4' => 'Pacific Ocean',
            'correct_option' => 4,
        ]);

        // Create a March quiz
        $quiz2 = Quiz::create([
            'name' => 'Quiz 2 - March',
            'start' => \Carbon\Carbon::create(2026, 3, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 3, 31, 17, 0, 0),
            'required_correct_answers' => 2,
        ]);

        // Add questions to quiz 2
        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'question' => 'What is the chemical symbol for water?',
            'option_1' => 'H2O',
            'option_2' => 'CO2',
            'option_3' => 'O2',
            'option_4' => 'N2',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'question' => 'How many continents are there?',
            'option_1' => '5',
            'option_2' => '6',
            'option_3' => '7',
            'option_4' => '8',
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'question' => 'What year did World War II end?',
            'option_1' => '1943',
            'option_2' => '1944',
            'option_3' => '1945',
            'option_4' => '1946',
            'correct_option' => 3,
        ]);
    }
}

