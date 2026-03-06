<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastReadAtToConversationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversation_user', function (Blueprint $table) {
        $table->timestamp('last_read_at')->nullable()->after('updated_at');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversation_user', function (Blueprint $table) {
        $table->dropColumn('last_read_at');
    });
    }
}
