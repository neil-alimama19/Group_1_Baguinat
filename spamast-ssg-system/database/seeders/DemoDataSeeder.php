<?php

namespace Database\Seeders;

use App\Models\Institute;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $officerRole = Role::firstOrCreate(['name' => 'Officer']);
        $adviserRole = Role::firstOrCreate(['name' => 'Adviser']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // 2. Create Institutes
        $ccs = Institute::firstOrCreate(
            ['code' => 'CCS'],
            ['name' => 'College of Computer Studies']
        );
        $coe = Institute::firstOrCreate(
            ['code' => 'COE'],
            ['name' => 'College of Education']
        );

        // 3. Create Programs
        $bsit = Program::firstOrCreate(
            ['code' => 'BSIT'],
            ['institute_id' => $ccs->id, 'name' => 'BS Information Technology']
        );
        $bscs = Program::firstOrCreate(
            ['code' => 'BSCS'],
            ['institute_id' => $ccs->id, 'name' => 'BS Computer Science']
        );
        $beed = Program::firstOrCreate(
            ['code' => 'BEED'],
            ['institute_id' => $coe->id, 'name' => 'Bachelor of Elementary Education']
        );

        // 4. Create Officer and Adviser accounts
        $password = Hash::make('Student@12345');

        User::firstOrCreate(
            ['email' => 'officer@spamast.edu.ph'],
            [
                'name' => 'Officer Account',
                'first_name' => 'Officer',
                'last_name' => 'Account',
                'password' => $password,
                'role_id' => $officerRole->id,
                'is_active' => true,
                'is_admin' => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'adviser@spamast.edu.ph'],
            [
                'name' => 'Adviser Account',
                'first_name' => 'Adviser',
                'last_name' => 'Account',
                'password' => $password,
                'role_id' => $adviserRole->id,
                'is_active' => true,
                'is_admin' => false,
            ]
        );

        // 5. Create 20 Students
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Luis', 'Carmen', 'Jorge', 'Elena'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Villanueva'];
        $programs = [$bsit, $bscs, $beed];

        for ($i = 1; $i <= 20; $i++) {
            // Cycle through names and programs
            $firstName = $firstNames[($i - 1) % 10];
            $lastName = $lastNames[($i - 1) % 10];
            $sex = ($i % 2 === 0) ? 'Female' : 'Male';
            $program = $programs[($i - 1) % 3];
            
            // Format student number like 2024-0001
            $studentNo = '2024-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $email = strtolower($firstName . '.' . $lastName . $i . '@spamast.edu.ph');

            // Create the User
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "$firstName $lastName",
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => $password,
                    'role_id' => $studentRole->id,
                    'is_active' => true,
                    'is_admin' => false,
                ]
            );

            // Create the Student profile linked to the User
            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'program_id' => $program->id,
                    'student_no' => $studentNo,
                    'sex' => $sex,
                    'year_level' => rand(1, 4),
                ]
            );
        }
    }
}