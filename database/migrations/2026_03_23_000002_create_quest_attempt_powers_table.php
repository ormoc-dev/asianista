<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestAttemptPowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quest_attempt_powers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quest_attempt_id');
            $table->string('power_name');
            $table->integer('level')->default(1);
            $table->timestamps();
            
            $table->foreign('quest_attempt_id')->references('id')->on('quest_attempts')->onDelete('cascade');
            $table->unique(['quest_attempt_id', 'power_name', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quest_attempt_powers');
    }
}
