<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\Institute;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'program.institute']);

        // Search by name or student_no
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('student_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
        }

        // Filter by institute
        if ($request->filled('institute_id')) {
            $instituteId = $request->input('institute_id');
            $query->whereHas('program', function ($q) use ($instituteId) {
                $q->where('institute_id', $instituteId);
            });
        }

        // Filter by program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->input('program_id'));
        }

        $students = $query->paginate(15)->withQueryString();
        
        // Pass data for filter dropdowns
        $institutes = Institute::orderBy('name')->get();
        $programs = Program::orderBy('name')->get(); // We will filter this in JS

        return view('officer.students.index', compact('students', 'institutes', 'programs'));
    }

    public function create()
    {
        $institutes = Institute::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();

        return view('officer.students.create', compact('institutes', 'programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'program_id' => 'required|exists:programs,id',
            'student_no' => 'required|string|max:255|unique:students,student_no',
            'sex' => 'required|in:Male,Female',
            'year_level' => 'required|integer|min:1|max:5',
        ]);

        DB::transaction(function () use ($validated) {
            $studentRole = Role::where('name', 'Student')->firstOrFail();

            // Create user account
            $user = User::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'password' => Hash::make($validated['student_no']), // Temporary password
                    'role_id' => $studentRole->id,
                    'is_active' => true,
                ]
            );

            // Create student profile
            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'program_id' => $validated['program_id'],
                    'student_no' => $validated['student_no'],
                    'sex' => $validated['sex'],
                    'year_level' => $validated['year_level'],
                ]
            );
        });

        Cache::forget('officer.stats'); // student count changed
        Cache::forget('admin.stats');   // a new Student account was created

        return redirect()->route('officer.students.index')->with('success', 'Student created successfully. Initial password is their student number.');
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $institutes = Institute::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();

        return view('officer.students.edit', compact('student', 'institutes', 'programs'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($student->user_id)],
            'program_id' => 'required|exists:programs,id',
            'student_no' => ['required', 'string', 'max:255', Rule::unique('students')->ignore($student->id)],
            'sex' => 'required|in:Male,Female',
            'year_level' => 'required|integer|min:1|max:5',
        ]);

        DB::transaction(function () use ($validated, $student) {
            // Update User
            $student->user->update([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
            ]);

            // Update Student
            $student->update([
                'program_id' => $validated['program_id'],
                'student_no' => $validated['student_no'],
                'sex' => $validated['sex'],
                'year_level' => $validated['year_level'],
            ]);
        });

        return redirect()->route('officer.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $userId = $student->user_id; // save it first, the row is deleted below

        DB::transaction(function () use ($student) {
            // Delete User (this will cascade and delete the student row too due to migration)
            $student->user->delete();
        });

        // The cascade also removed this student's attendances and penalties
        Cache::forget('officer.stats');
        Cache::forget('adviser.stats');
        Cache::forget('admin.stats');
        Cache::forget("student.stats.{$userId}");

        return redirect()->route('officer.students.index')->with('success', 'Student deleted successfully.');
    }
}