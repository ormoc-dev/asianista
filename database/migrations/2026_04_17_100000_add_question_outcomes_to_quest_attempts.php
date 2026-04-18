<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quest_attempts')) {
            return;
        }

        Schema::table('quest_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('quest_attempts', 'question_outcomes')) {
                $table->json('question_outcomes')->nullable()->after('score');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('quest_attempts')) {
            return;
        }

        Schema::table('quest_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quest_attempts', 'question_outcomes')) {
                $table->dropColumn('question_outcomes');
            }
        });
    }
};
