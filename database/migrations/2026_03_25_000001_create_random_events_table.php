<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('random_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('effect'); // Description of the effect
            $table->integer('xp_reward')->default(0);
            $table->integer('xp_penalty')->default(0);
            $table->enum('target_type', ['single', 'all', 'pair', 'random'])->default('single');
            $table->enum('event_type', ['positive', 'negative', 'neutral', 'challenge'])->default('neutral');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('random_events');
    }
};
