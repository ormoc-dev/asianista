<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapPinsToQuestsTable extends Migration
{
    public function up()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->json('map_pins')->nullable()->after('map_image');
        });
    }

    public function down()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn('map_pins');
        });
    }
}
