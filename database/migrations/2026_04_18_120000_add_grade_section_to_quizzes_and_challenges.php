<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->after('teacher_id')->constrained('grades')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('grade_id')->constrained('sections')->nullOnDelete();
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->after('description')->constrained('grades')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('grade_id')->constrained('sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['grade_id', 'section_id']);
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['grade_id', 'section_id']);
        });
    }
};
