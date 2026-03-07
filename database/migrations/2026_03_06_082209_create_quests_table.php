<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('difficulty')->default('medium');
            $table->integer('level')->default(1);
            $table->integer('xp_reward')->default(0);
            $table->integer('ab_reward')->default(0);
            $table->integer('gp_reward')->default(0);
            $table->dateTime('assign_date');
            $table->dateTime('due_date');
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
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
        Schema::dropIfExists('quests');
    }
}
