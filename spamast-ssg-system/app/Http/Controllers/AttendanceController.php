<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    /**
     * Shared view for Officers and Advisers
     */
    public function index(Request $request, Event $event)
    {
        // 0. "Clear Filters" link sends ?clear=1: forget saved filters, then reload a clean URL
        if ($request->boolean('clear')) {
            session()->forget(['filters.institute_id', 'filters.program_id']);

            return redirect()->route('attendance.index', $event);
        }

        // Resolve institute_id and program_id: request first, otherwise the saved session value
        foreach (['institute_id', 'program_id'] as $key) {
            if ($request->has($key)) {
                // The filter form was submitted: save the choice back to the session
                if ($request->filled($key)) {
                    session()->put("filters.$key", $request->input($key));
                } else {
                    // "All Institutes" / "All Programs" was chosen: drop the saved value
                    session()->forget("filters.$key");
                }
            } elseif (session()->has("filters.$key")) {
                // Nothing in the URL: reuse the saved filter. merge() lets the queries,
                // the dropdown "selected" checks and pagination links all see it.
                $request->merge([$key => session("filters.$key")]);
            }
        }

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
            return redirect()->route('attendance.index', $event)
                ->with('error', 'An attendance record already exists for this student and session.');
        }

        Attendance::create($validated);

        Cache::forget('adviser.stats'); // attendance record count changed
        // Forget the key of the student who received the record
        $userId = Student::whereKey($validated['student_id'])->value('user_id');
        Cache::forget("student.stats.{$userId}");

        // Back to this event's attendance list (the feature's index)
        return redirect()->route('attendance.index', $event)->with('success', 'Attendance record added successfully.');
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

        // The record's session knows which event it belongs to
        return redirect()->route('attendance.index', $attendance->session->event_id)
            ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Officer: Delete a record
     */
    public function destroy(Attendance $attendance)
    {
        Gate::authorize('manage-ssg');

        $userId = $attendance->student->user_id;     // save it before deleting
        $eventId = $attendance->session->event_id;   // save it before deleting

        $attendance->delete();

        Cache::forget('adviser.stats');
        Cache::forget("student.stats.{$userId}");

        return redirect()->route('attendance.index', $eventId)->with('success', 'Attendance record deleted successfully.');
    }
}