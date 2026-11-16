<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CATEGORIES = [
        ['period' => 'december-january', 'name' => 'Викторины художественной направленности', 'image' => 'quiz_categories/arts.webp'],
        ['period' => 'january-february', 'name' => 'Викторины социально-гуманитарной направленности', 'image' => 'quiz_categories/humanities.webp'],
        ['period' => 'january-february', 'name' => 'Викторины туристско-краеведческой направленности', 'image' => 'quiz_categories/local.webp'],
        ['period' => 'february-march', 'name' => 'Викторины технической направленности', 'image' => 'quiz_categories/technology.webp'],
        ['period' => 'march-april', 'name' => 'Викторины физкультурно-спортивной направленности', 'image' => 'quiz_categories/sports.webp'],
    ];

    public function up(): void
    {
        $quizzes = json_decode(file_get_contents(database_path('data/arts-2026-2027.json')), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($quizzes) {
            $timestamps = ['created_at' => now(), 'updated_at' => now()];

            foreach (self::CATEGORIES as $category) {
                $categoryId = DB::table('quiz_categories')->insertGetId($category + $timestamps);

                if ($category['period'] !== 'december-january') {
                    continue;
                }

                foreach ($quizzes as $quiz) {
                    $quizId = DB::table('quizzes')->insertGetId([
                        'name' => $quiz['name'],
                        'category_id' => $categoryId,
                        'start' => '2026-12-01 00:00:00',
                        'end' => '2027-01-31 23:59:59',
                        'time_limit' => 12,
                        'required_correct_answers' => (int) ceil(count($quiz['questions']) * 0.9),
                        'certificate_image' => $quiz['certificate_image'],
                        'diploma_image' => 'arts-2026-2027.jpg',
                    ] + $timestamps);

                    DB::table('quiz_questions')->insert(array_map(
                        fn ($question) => $question + ['quiz_id' => $quizId] + $timestamps,
                        $quiz['questions']
                    ));
                }
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $categories = DB::table('quiz_categories')
                ->whereIn('period', array_column(self::CATEGORIES, 'period'))
                ->whereIn('image', array_column(self::CATEGORIES, 'image'))
                ->pluck('id');

            $seededQuizzes = DB::table('quizzes')
                ->whereIn('category_id', $categories)
                ->whereIn('certificate_image', ['art-experts.jpg', 'art-without-borders.jpg', 'art-in-detail.jpg']);
            $quizIds = $seededQuizzes->pluck('id');

            if (DB::table('quiz_participants')->whereIn('quiz_id', $quizIds)->exists()
                || DB::table('quizzes')->whereIn('category_id', $categories)->whereNotIn('id', $quizIds)->exists()) {
                throw new RuntimeException('The quiz season contains participant results or additional quizzes.');
            }

            $seededQuizzes->delete();
            DB::table('quiz_categories')->whereIn('id', $categories)->delete();
        });
    }
};
