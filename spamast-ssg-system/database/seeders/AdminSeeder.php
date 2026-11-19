<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the Admin role exists, and get its ID
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // 2. Create the Admin user using firstOrCreate to prevent duplicates
        User::firstOrCreate(
            ['email' => 'admin@spamast.edu.ph'], // Search criteria
            [
                'name' => 'System Administrator', // Required by Laravel default
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => Hash::make('Admin@12345'),
                'is_admin' => true,
                'role_id' => $adminRole->id,
            ]
        );
    }
}