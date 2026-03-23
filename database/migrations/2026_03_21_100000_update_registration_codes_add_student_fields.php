<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRegistrationCodesAddStudentFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('code');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('middle_name')->nullable()->after('last_name');
            $table->string('username')->unique()->nullable()->after('middle_name');
            $table->string('default_password')->nullable()->after('username');
            $table->string('student_code')->unique()->nullable()->after('default_password');
            $table->string('character')->nullable()->after('student_code');
            $table->string('gender')->nullable()->after('character');
            $table->foreignId('user_id')->nullable()->after('gender')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'middle_name',
                'username',
                'default_password',
                'student_code',
                'character',
                'gender',
                'user_id',
            ]);
        });
    }
}
