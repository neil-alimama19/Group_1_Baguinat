<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('venue');
            $table->date('event_date');
            $table->enum('day_type', ['whole_day', 'half_day']);
            $table->enum('availability', ['scheduled', 'manual'])->default('scheduled');
            $table->decimal('fine_per_miss', 8, 2)->default(0);
            // Foreign key to users, allows null, sets to null if the user is deleted
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};