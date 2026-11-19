<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            // Cascade delete means if the event is deleted, sessions are deleted too
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->enum('period', ['morning', 'afternoon', 'half_day']);
            $table->time('check_in_start')->nullable();
            $table->time('check_in_end')->nullable();
            $table->time('check_out_start')->nullable();
            $table->time('check_out_end')->nullable();
            $table->boolean('check_in_open')->default(false);
            $table->boolean('check_out_open')->default(false);
            $table->timestamps();

            // Unique index to prevent duplicate periods for the same event
            $table->unique(['event_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sessions');
    }
};