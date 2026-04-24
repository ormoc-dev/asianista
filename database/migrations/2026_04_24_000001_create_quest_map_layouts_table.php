<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateQuestMapLayoutsTable extends Migration
{
    public function up()
    {
        Schema::create('quest_map_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('map_key')->unique();
            $table->json('pins');
            $table->timestamps();
        });

        $defaultPins = [
            ['left' => 50, 'top' => 86, 'name' => 'Gate of Entry', 'icon' => 'fa-mountain'],
            ['left' => 25, 'top' => 55, 'name' => 'Whispering Falls', 'icon' => 'fa-water'],
            ['left' => 15, 'top' => 66, 'name' => 'Compass Grove', 'icon' => 'fa-compass'],
            ['left' => 40, 'top' => 40, 'name' => 'Floating Reaches', 'icon' => 'fa-cloud'],
            ['left' => 55, 'top' => 60, 'name' => 'Sky-Isle Steps', 'icon' => 'fa-shoe-prints'],
            ['left' => 75, 'top' => 45, 'name' => 'Mystery Landmark', 'icon' => 'fa-question'],
            ['left' => 75, 'top' => 80, 'name' => 'Trivia Chamber', 'icon' => 'fa-brain'],
            ['left' => 85, 'top' => 65, 'name' => 'Library of Wisdom', 'icon' => 'fa-book'],
            ['left' => 80, 'top' => 20, 'name' => 'The Observatory', 'icon' => 'fa-crown'],
        ];

        DB::table('quest_map_layouts')->insert([
            'map_key' => 'default',
            'pins' => json_encode($defaultPins),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('quest_map_layouts');
    }
}
