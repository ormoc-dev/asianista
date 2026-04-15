<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Teacher-created lessons are published immediately; default status is approved.
     * Existing pending rows are promoted so they remain visible to students.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        DB::table('lessons')->where('status', 'pending')->update(['status' => 'approved']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lessons MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lessons MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
