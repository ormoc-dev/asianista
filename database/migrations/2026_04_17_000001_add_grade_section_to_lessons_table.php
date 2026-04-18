<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'grade_id')) {
                $table->unsignedBigInteger('grade_id')->nullable()->after('teacher_id');
            }
            if (! Schema::hasColumn('lessons', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('grade_id');
            }
        });

        if (! Schema::hasTable('grades') || ! Schema::hasTable('sections')) {
            return;
        }

        $lessons = DB::table('lessons')
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->get(['id', 'section']);

        foreach ($lessons as $row) {
            $parts = explode(' - ', $row->section, 2);
            if (count($parts) !== 2) {
                continue;
            }
            $gradeId = DB::table('grades')->where('name', trim($parts[0]))->value('id');
            if (! $gradeId) {
                continue;
            }
            $sectionId = DB::table('sections')
                ->where('grade_id', $gradeId)
                ->where('name', trim($parts[1]))
                ->value('id');
            if ($sectionId) {
                DB::table('lessons')->where('id', $row->id)->update([
                    'grade_id' => $gradeId,
                    'section_id' => $sectionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'section_id')) {
                $table->dropColumn('section_id');
            }
            if (Schema::hasColumn('lessons', 'grade_id')) {
                $table->dropColumn('grade_id');
            }
        });
    }
};
