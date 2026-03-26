<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventDrawHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_draw_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('random_event_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('event_title');
            $table->text('event_description');
            $table->string('event_type');
            $table->integer('xp_reward')->default(0);
            $table->integer('xp_penalty')->default(0);
            $table->string('target_type');
            $table->text('effect');
            $table->timestamps();

            $table->foreign('random_event_id')->references('id')->on('random_events')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_draw_histories');
    }
}
