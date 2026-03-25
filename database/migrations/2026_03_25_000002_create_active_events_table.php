<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('active_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('random_event_id')->constrained('random_events')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('affected_students')->nullable(); // Track which students were affected
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('active_events');
    }
};
