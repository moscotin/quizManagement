<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoQuizWithAllTypesSeeder extends Seeder
{
    public function run(): void
    {

        // 2) Quiz (adjust fields to your schema)
        // If your Quiz model requires other fields (title/start/time_limit/etc.), update them here.
        $quiz = Quiz::create([
            'name' => 'Demo Quiz: all 4 question types',
            'category_id' => 1,
            // start on Feb 1, 2026
            'start' => \Carbon\Carbon::create(2026, 2, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 2, 28, 17, 0, 0),
            'time_limit' => 12, // minutes
            'required_correct_answers' => 3,
            // 'is_active' => true,
        ]);

        // 3) Questions

        // A) single_choice
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'single_choice',
            'question' => 'Single choice: What is 2 + 2?',
            'option_1' => '3',
            'option_2' => '4',
            'option_3' => '5',
            'option_4' => '22',
            'option_5' => null,
            'option_6' => null,
            'correct_option' => 2, // "4"
            'correct_options' => null,
            'correct_answer' => null,
            'matching_pairs' => null,
            'image' => null,
        ]);

        // B) multiple_choice (correct_options is JSON array of indices)
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'multiple_choice',
            'question' => 'Multiple choice: Select all prime numbers.',
            'option_1' => '2',
            'option_2' => '3',
            'option_3' => '4',
            'option_4' => '5',
            'option_5' => '6',
            'option_6' => null,
            'correct_option' => null,
            'correct_options' => [1, 2, 4], // 2, 3, 5
            'correct_answer' => null,
            'matching_pairs' => null,
            'image' => null,
        ]);

        // C) fill_in_the_blank (text)
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'fill_in_the_blank',
            'question' => 'Fill in the blank: The capital of Lithuania is _____.',
            'option_1' => null,
            'option_2' => null,
            'option_3' => null,
            'option_4' => null,
            'option_5' => null,
            'option_6' => null,
            'correct_option' => null,
            'correct_options' => null,
            'correct_answer' => 'Vilnius',
            'matching_pairs' => null,
            'image' => null,
        ]);

        // D) matching (matching_pairs JSON)
        // This format matches the take.blade.php renderer I gave you:
        // array of {left,right} pairs
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'matching',
            'question' => 'Matching: Match the country to its capital.',
            'option_1' => null,
            'option_2' => null,
            'option_3' => null,
            'option_4' => null,
            'option_5' => null,
            'option_6' => null,
            'correct_option' => null,
            'correct_options' => null,
            'correct_answer' => null,
            'matching_pairs' => [
                ['left' => 'Lithuania', 'right' => 'Vilnius'],
                ['left' => 'France', 'right' => 'Paris'],
                ['left' => 'Germany', 'right' => 'Berlin'],
            ],
            'image' => null,
        ]);
    }
}
