<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('section', 120)->nullable()->after('middle_name');
        });

        Schema::table('registration_codes', function (Blueprint $table) {
            $table->string('section', 120)->nullable()->after('middle_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('section');
        });

        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }
};
