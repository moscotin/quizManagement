<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\QuizQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizCatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuizCategory::create([
            'name' => 'викторины физкультурно-спортивной направленности',
            'image' => 'quiz_categories/1.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (английский язык)',
            'image' => 'quiz_categories/2.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (немецкий язык)',
            'image' => 'quiz_categories/3.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины художественной направленности',
            'image' => 'quiz_categories/4.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (китайский язык)',
            'image' => 'quiz_categories/5.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (японский язык)',
            'image' => 'quiz_categories/6.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (волонтерское движение)',
            'image' => 'quiz_categories/7.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (испанский язык)',
            'image' => 'quiz_categories/8.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (итальянский язык)',
            'image' => 'quiz_categories/9.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины социально-гуманитарной направленности (французский язык)',
            'image' => 'quiz_categories/10.webp',
        ]);

        QuizCategory::create([
            'name' => 'викторины технической направленности',
            'image' => 'quiz_categories/11.webp',
        ]);
    }
}

