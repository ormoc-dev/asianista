<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'section')) {
                $table->dropColumn('section');
            }
            $table->foreignId('grade_id')->nullable()->after('middle_name')->constrained('grades')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('grade_id')->constrained('sections')->nullOnDelete();
        });

        Schema::table('registration_codes', function (Blueprint $table) {
            if (Schema::hasColumn('registration_codes', 'section')) {
                $table->dropColumn('section');
            }
            $table->foreignId('grade_id')->nullable()->after('middle_name')->constrained('grades')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('grade_id')->constrained('sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['grade_id', 'section_id']);
            $table->string('section', 120)->nullable()->after('middle_name');
        });

        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['grade_id', 'section_id']);
            $table->string('section', 120)->nullable()->after('middle_name');
        });
    }
};
