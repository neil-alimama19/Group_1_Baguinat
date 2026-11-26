<?php

namespace Database\Seeders;

use App\Models\Institute;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles exist
        Role::firstOrCreate(['name' => 'Admin']);
        $officerRole = Role::firstOrCreate(['name' => 'Officer']);
        $adviserRole = Role::firstOrCreate(['name' => 'Adviser']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // 2. SPAMAST institutes and their programs.
        //    Format: 'INSTITUTE CODE' => ['name' => ..., 'programs' => ['PROGRAM CODE' => 'Program name']]
        $catalog = [
            'IASDC' => [
                'name' => 'Institute of Agricultural Sciences and Development Communication',
                'programs' => [
                    'BSA'  => 'Bachelor of Science in Agriculture',
                    'BAT'  => 'Bachelor of Agricultural Technology',
                    'BSDC' => 'Bachelor of Science in Development Communication',
                ],
            ],
            'IFMS' => [
                'name' => 'Institute of Fisheries and Marine Sciences',
                'programs' => [
                    'BSF'  => 'Bachelor of Science in Fisheries',
                    'BSMB' => 'Bachelor of Science in Marine Biology',
                    'BSES' => 'Bachelor of Science in Environmental Science',
                    'DFT'  => 'Diploma in Fisheries Technology',
                ],
            ],
            'IBMG' => [
                'name' => 'Institute of Business Management and Governance',
                'programs' => [
                    'BSAB'  => 'Bachelor of Science in Agribusiness',
                    'BPA'   => 'Bachelor of Public Administration',
                    'BSOA'  => 'Bachelor of Science in Office Administration',
                    'BSAIS' => 'Bachelor of Science in Accounting Information System',
                    'BSTM'  => 'Bachelor of Science in Tourism Management',
                ],
            ],
            'ICET' => [
                'name' => 'Institute of Computing, Engineering, and Technology',
                'programs' => [
                    'BSIT'     => 'Bachelor of Science in Information Technology',
                    'BSABE'    => 'Bachelor of Science in Agricultural and Biosystems Engineering',
                    'BIndTech' => 'Bachelor of Industrial Technology major in Computer Technology',
                    'ACT'      => 'Associate in Computer Technology',
                ],
            ],
            'ITEd' => [
                'name' => 'Institute of Teacher Education',
                'programs' => [
                    'BEEd'      => 'Bachelor of Elementary Education',
                    'BSEd-ENG'  => 'Bachelor of Secondary Education major in English',
                    'BSEd-MATH' => 'Bachelor of Secondary Education major in Mathematics',
                    'BSEd-SCI'  => 'Bachelor of Secondary Education major in Science',
                    'BTLEd-HE'  => 'Bachelor of Technology and Livelihood Education major in Home Economics',
                    'BTLEd-AFA' => 'Bachelor of Technology and Livelihood Education major in Agri-Fishery Arts',
                ],
            ],
            'IHS' => [
                'name' => 'Institute of Human Services',
                'programs' => [
                    'BSCrim' => 'Bachelor of Science in Criminology',
                    'BSSW'   => 'Bachelor of Science in Social Work',
                ],
            ],
        ];

        foreach ($catalog as $instituteCode => $data) {
            // updateOrCreate: creates it, or fixes the name if the code already exists (no duplicates)
            $institute = Institute::updateOrCreate(
                ['code' => $instituteCode],
                ['name' => $data['name']]
            );

            foreach ($data['programs'] as $programCode => $programName) {
                // An old program with the same code (such as BSIT or BEEd) moves to its new institute
                Program::updateOrCreate(
                    ['code' => $programCode],
                    ['institute_id' => $institute->id, 'name' => $programName]
                );
            }
        }

        // 3. Remove the old sample data (CCS, COE, BSCS) that is not in the SPAMAST list
        $this->removeOldSampleData();

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

        // 5. Create sample Students spread across all programs.
        //    Change this number to make more or fewer students. Emails and student numbers are always
        //    unique; only 100 name combinations exist, so a few full names repeat after 90 students.
        $studentCount = 60;

        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Luis', 'Carmen', 'Jorge', 'Elena'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Villanueva'];
        $programs = Program::orderBy('id')->get(); // every program, so each institute has students

        for ($i = 1; $i <= $studentCount; $i++) {
            // Cycle through names and programs.
            // After the first 20 students the last name shifts, so full names do not repeat.
            $shift = max(0, intdiv($i - 1, 10) - 1);
            $firstName = $firstNames[($i - 1) % 10];
            $lastName = $lastNames[(($i - 1) % 10 + $shift) % 10];
            $sex = ($i % 2 === 0) ? 'Female' : 'Male';
            $program = $programs[($i - 1) % $programs->count()];

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

        // The Admin dashboard counts institutes and programs, so clear its saved numbers
        Cache::forget('admin.stats');
        Cache::forget('officer.stats');
    }

    /**
     * Delete the earlier sample institutes/programs (CCS, COE, BSCS).
     * Students on BSCS are moved to BSIT first, because a program with students cannot be deleted.
     */
    private function removeOldSampleData(): void
    {
        $oldProgram = Program::where('code', 'BSCS')->first();
        $bsit = Program::where('code', 'BSIT')->first();

        if ($oldProgram && $bsit) {
            Student::where('program_id', $oldProgram->id)->update(['program_id' => $bsit->id]);
            $oldProgram->delete();
        }

        // Delete an old institute only when it has no programs left
        foreach (['CCS', 'COE'] as $code) {
            $institute = Institute::where('code', $code)->first();

            if ($institute && ! $institute->programs()->exists()) {
                $institute->delete();
            }
        }
    }
}