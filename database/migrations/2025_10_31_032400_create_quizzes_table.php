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
            
            // Basic quiz info
            $table->string('title'); // Quiz title
            $table->text('description')->nullable(); // Optional description
            
            // File upload path
            $table->string('file_path')->nullable(); // Stores uploaded file path
            
            // Quiz type: quiz, pre-test, or post-test
            $table->enum('type', ['quiz', 'pre-test', 'post-test'])->default('quiz');
            
            // Status (pending, active, inactive)
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            
            // Schedule
            $table->dateTime('assign_date')->nullable(); // When quiz becomes available
            $table->dateTime('due_date')->nullable();    // When quiz closes
            
            // Relationship (link to teacher)
            $table->unsignedBigInteger('teacher_id')->nullable();
            
            $table->timestamps();

            // Foreign key (optional, if users table exists)
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
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
