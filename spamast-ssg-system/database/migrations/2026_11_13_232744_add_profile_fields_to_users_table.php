<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            
            // Adding the fields mentioned in your rules so they exist
            $table->integer('role_id')->default(2)->after('last_name'); // 2 = Student
            $table->boolean('is_active')->default(true)->after('role_id');
            $table->boolean('is_admin')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 
                'middle_name', 
                'last_name', 
                'role_id', 
                'is_active', 
                'is_admin'
            ]);
        });
    }
};