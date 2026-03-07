<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $row) {
            $row->id();
            $row->string('key')->unique();
            $row->text('value')->nullable();
            $row->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
