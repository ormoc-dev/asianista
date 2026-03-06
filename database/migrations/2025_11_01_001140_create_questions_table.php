<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id'); // link to quiz
            $table->text('question'); // the question text
            $table->enum('type', ['multiple_choice', 'identification']); // question type
            $table->json('choices')->nullable(); // for multiple choice options
            $table->string('correct_answer'); // correct answer
            $table->text('direction')->nullable(); // instructions or context for question
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
