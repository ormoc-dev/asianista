<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('active_events', function (Blueprint $table) {
            $table->string('recipient_mode', 20)->default('all')->after('affected_students');
            $table->json('recipient_student_ids')->nullable()->after('recipient_mode');
        });

        Schema::table('event_draw_histories', function (Blueprint $table) {
            $table->string('recipient_mode', 20)->default('all')->after('effect');
            $table->json('recipient_student_ids')->nullable()->after('recipient_mode');
        });
    }

    public function down(): void
    {
        Schema::table('active_events', function (Blueprint $table) {
            $table->dropColumn(['recipient_mode', 'recipient_student_ids']);
        });

        Schema::table('event_draw_histories', function (Blueprint $table) {
            $table->dropColumn(['recipient_mode', 'recipient_student_ids']);
        });
    }
};
