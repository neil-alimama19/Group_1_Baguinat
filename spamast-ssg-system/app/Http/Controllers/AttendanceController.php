<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    /**
     * Shared view for Officers and Advisers
     */
    public function index(Request $request, Event $event)
    {
        // 1. Start query with eager loading
        $query = Attendance::with(['student.user', 'student.program.institute', 'session'])
            ->whereHas('session', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            });

        // 2. Filter by Session
        if ($request->filled('session_id')) {
            $query->where('event_session_id', $request->input('session_id'));
        }

        // 3. Filter by Institute
        if ($request->filled('institute_id')) {
            $instituteId = $request->input('institute_id');
            $query->whereHas('student.program', function ($q) use ($instituteId) {
                $q->where('institute_id', $instituteId);
            });
        }

        // 4. Filter by Program
        if ($request->filled('program_id')) {
            $programId = $request->input('program_id');
            $query->whereHas('student', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }

        $attendances = $query->paginate(20)->withQueryString();
        
        // Data for filter dropdowns
        $sessions = $event->sessions;
        $institutes = \App\Models\Institute::all();
        $programs = \App\Models\Program::all(); // Filtered by JS based on selected institute

        return view('attendance.index', compact('event', 'attendances', 'sessions', 'institutes', 'programs'));
    }

    /**
     * Officer: Add a missing record
     */
    public function store(Request $request, Event $event)
    {
        Gate::authorize('manage-ssg');

        $validated = $request->validate([
            'event_session_id' => 'required|exists:event_sessions,id',
            'student_id' => 'required|exists:students,id',
            'check_in_at' => 'nullable|date',
            'check_out_at' => 'nullable|date|after_or_equal:check_in_at',
        ]);

        // Ensure session belongs to this event
        $session = EventSession::where('id', $validated['event_session_id'])
                               ->where('event_id', $event->id)
                               ->firstOrFail();

        // Check for duplicate
        $exists = Attendance::where('event_session_id', $session->id)
                            ->where('student_id', $validated['student_id'])
                            ->exists();

        if ($exists) {
            return back()->with('error', 'An attendance record already exists for this student and session.');
        }

        Attendance::create($validated);

        return back()->with('success', 'Attendance record added successfully.');
    }

    /**
     * Officer: Correct times
     */
    public function update(Request $request, Attendance $attendance)
    {
        Gate::authorize('manage-ssg');

        $validated = $request->validate([
            'check_in_at' => 'nullable|date',
            'check_out_at' => 'nullable|date|after_or_equal:check_in_at',
        ]);

        $attendance->update($validated);

        return back()->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Officer: Delete a record
     */
    public function destroy(Attendance $attendance)
    {
        Gate::authorize('manage-ssg');

        $attendance->delete();

        return back()->with('success', 'Attendance record deleted successfully.');
    }
}