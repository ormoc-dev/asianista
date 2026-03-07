<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quest_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->string('type'); // multiple-choice, identification
            $table->integer('points')->default(0);
            $table->json('options')->nullable(); // For multiple choice
            $table->string('answer'); // Correct answer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quest_questions');
    }
}
