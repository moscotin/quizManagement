<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FebDeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a February quiz
        $quiz1 = Quiz::create([
            'name' => 'Deutsche Märchen',
            'category_id' => 3,
            'start' => \Carbon\Carbon::create(2026, 2, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 2, 28, 17, 0, 0),
            'required_correct_answers' => 13,
            'certificate_image' => 'feb_de1.jpg',
        ]);

        $quiz2 = Quiz::create([
            'name' => 'Grammatiktest',
            'category_id' => 3,
            'start' => \Carbon\Carbon::create(2026, 2, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 2, 28, 17, 0, 0),
            'required_correct_answers' => 13,
            'certificate_image' => 'feb_de2.jpg',
        ]);

        $quiz3 = Quiz::create([
            'name' => 'Tierquiz',
            'category_id' => 3,
            'start' => \Carbon\Carbon::create(2026, 2, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 2, 28, 17, 0, 0),
            'required_correct_answers' => 13,
            'certificate_image' => 'feb_de3.jpg',
        ]);

        $quiz4 = Quiz::create([
            'name' => 'Anfänger-Quiz',
            'category_id' => 3,
            'start' => \Carbon\Carbon::create(2026, 2, 1, 9, 0, 0),
            'end' => \Carbon\Carbon::create(2026, 2, 28, 17, 0, 0),
            'required_correct_answers' => 13,
            'certificate_image' => 'feb_de4.jpg',
        ]);

        // Add questions to quiz 1
        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wie hieβen die Brüder Grimm?',
            'option_1' => 'Jacob und Friedrich',
            'option_2' => 'Wilhelm und Jacob',
            'option_3' => 'Wilhelm und Friedrich',
            'option_4' => '',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wann wurde der erste Band der Märchen der Brüder Grimm veröffentlicht?',
            'option_1' => '1820',
            'option_2' => '1812',
            'option_3' => '1810',
            'option_4' => '',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wer ist das? Dieses Mädchen war immer schmutzig. Sie hatte keine Kleider und kann nicht tanzen. Sie sollte immer das Geschirr waschen.',
            'option_1' => 'Aschenputtel ',
            'option_2' => 'Schneewittchen ',
            'option_3' => 'Rotkäppchen',
            'option_4' => '',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'In welchem Märchen gibt es den Satz: Da nahm sie den Krug von der Wand und ging in den Keller. ',
            'option_1' => 'Die kluge Else',
            'option_2' => 'Der gescheite Hans',
            'option_3' => 'Der süße Brei',
            'option_4' => '',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wann wurde das "Deutsche Wörterbuch" der Brüder Grimm beendet?',
            'option_1' => '30 Jahre nach ihrem Tod',
            'option_2' => '50 Jahre nach ihrem Tod',
            'option_3' => '100 Jahre nach ihrem Tod',
            'option_4' => '',
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'In welchem Märchen gibt es den Satz:  Der Baum rief: "Schüttle mich, die Äpfel sind alle reif".',
            'option_1' => 'Daumesdick',
            'option_2' => 'Frau Holle',
            'option_3' => 'Der süße Brei',
            'option_4' => '',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Welche deutsche Stadt erinnert uns an den berühmten deutschen Rattenfänger?',
            'option_1' => ' Hameln',
            'option_2' => ' Minden',
            'option_3' => ' Göttingen',
            'option_4' => '',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wem hat Jakob das Leben gerettet?',
            'option_1' => 'einer Ente',
            'option_2' => 'einer Gans',
            'option_3' => 'einem Schwan',
            'option_4' => '',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'In welchem Märchen gibt es die Worte: Das Mädchen brachte den Topf nach Hause und sie waren jetzt nie hungrig. ',
            'option_1' => 'Der süße Brei',
            'option_2' => 'Die drei Spinnerinnen',
            'option_3' => 'Die Bremer Stadtmusikanten',
            'option_4' => '',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Was schenkte Frau Holle der zweiten Tochter?',
            'option_1' => 'die goldene Gans ',
            'option_2' => 'das Pech',
            'option_3' => 'die Mühle',
            'option_4' => '',
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'In welchem Märchen gibt es die Worte:  Spieglein, Spieglein an der Wand, wer ist die Schönste im ganzen Land?',
            'option_1' => 'Daumesdick',
            'option_2' => 'Frau Holle',
            'option_3' => 'Schneewittchen',
            'option_4' => '',
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wann wurde Wilhelm Hauff geboren?',
            'option_1' => 'am 29.November 1802 ',
            'option_2' => 'am 28.Oktober 1892 ',
            'option_3' => 'am 23.November 1825',
            'option_4' => '',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wie viele Museen gibt es in Deutschland zu Ehren der Brüder Grimm?',
            'option_1' => 'drei',
            'option_2' => 'eins ',
            'option_3' => 'zwei',
            'option_4' => '',
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Wie bekam der König die Eselsohren und die lange Nase im Märchen "Der kleine Muck"?',
            'option_1' => 'Er aß die Zauberbeeren',
            'option_2' => 'Er trank das Wasser aus dem Krug',
            'option_3' => 'Er hörte nicht auf den Zauberer',
            'option_4' => '',
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Was hat Zwerg Nase am Hof des Herzogs beruflich gemacht?',
            'option_1' => 'Er war Gehilfe des Küchenchefs',
            'option_2' => 'Er war Höfling',
            'option_3' => 'Er war Musiker',
            'option_4' => '',
            'correct_option' => 1,
        ]);

    }
}

