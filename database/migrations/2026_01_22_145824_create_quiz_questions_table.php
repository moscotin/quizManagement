<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quiz_id')
                ->constrained('quizzes')
                ->cascadeOnDelete();

            $table->text('question');

            // Options for single & multiple choice questions
            $table->string('option_1')->nullable();
            $table->string('option_2')->nullable();
            $table->string('option_3')->nullable();
            $table->string('option_4')->nullable();
            $table->string('option_5')->nullable();
            $table->string('option_6')->nullable();

            // Correct option index for single choice questions (1–6)
            $table->unsignedTinyInteger('correct_option')->nullable();

            // Correct options for multiple choice questions (e.g. [1,3,4])
            $table->json('correct_options')->nullable();

            // Correct answer for text / fill-in-the-blank questions
            $table->string('correct_answer')->nullable();

            // Matching pairs for matching questions
            $table->json('matching_pairs')->nullable();

            // Optional image per question
            $table->string('image')->nullable();

            // Question type
            // single_choice | multiple_choice | fill_in_the_blank | matching
            $table->string('question_type')->default('single_choice');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
