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
        Schema::create('quiz_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participant_id')
                ->constrained('quiz_participants')
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('quiz_questions')
                ->cascadeOnDelete();

            // For single choice (1–6)
            $table->unsignedTinyInteger('selected_option')->nullable();

            // For multiple choice (e.g. [1,3,4])
            $table->json('selected_options')->nullable();

            // For fill-in-the-blank / text answers
            $table->text('answer_text')->nullable();

            // For matching questions (structure depends on your frontend)
            $table->json('matching_response')->nullable();

            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['participant_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_responses');
    }
};
