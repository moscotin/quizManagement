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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('category_id')->unsigned();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->integer('time_limit')->default(20); // in minutes
            // 4 images
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            // image positioning
            $table->string('image1_class')->nullable();
            $table->string('image2_class')->nullable();
            $table->string('image3_class')->nullable();
            $table->string('image4_class')->nullable();
            // required correct answers to get a diploma
            $table->integer('required_correct_answers');
            // diploma image
            $table->string('diploma_image')->nullable();
            // certificate image
            $table->string('certificate_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
