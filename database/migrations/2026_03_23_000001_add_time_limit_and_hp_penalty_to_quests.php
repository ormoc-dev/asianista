<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeLimitAndHpPenaltyToQuests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->integer('time_limit_minutes')->nullable()->after('gp_reward')->comment('Time limit per level in minutes');
            $table->integer('hp_penalty')->default(10)->after('time_limit_minutes')->comment('HP deducted per wrong answer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn(['time_limit_minutes', 'hp_penalty']);
        });
    }
}
