<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Penalty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function index()
    {
        Gate::authorize('view-reports');

        // Show all past and current events
        $events = Event::orderByDesc('event_date')->paginate(15);

        return view('reports.index', compact('events'));
    }

    public function show(Event $event)
    {
        Gate::authorize('view-reports');

        // 3. Query Builder for attendance summary grouped by institute and program
        // We use 'attendances as a' to keep the selectRaw clean
        $attendanceSummary = DB::table('attendances as a')
            ->join('event_sessions as es', 'a.event_session_id', '=', 'es.id')
            ->join('students as s', 'a.student_id', '=', 's.id')
            ->join('programs as p', 's.program_id', '=', 'p.id')
            ->join('institutes as i', 'p.institute_id', '=', 'i.id')
            ->where('es.event_id', $event->id)
            ->whereNotNull('a.check_in_at') // Keep only rows with check_in_at
            ->groupBy('i.code', 'p.code')
            // 4. Use COUNT(DISTINCT a.student_id) for total, and CASE for male/female
            ->selectRaw('
                i.code as institute_code,
                p.code as program_code,
                COUNT(DISTINCT a.student_id) as total,
                COUNT(DISTINCT CASE WHEN s.sex = "Male" THEN a.student_id END) as male,
                COUNT(DISTINCT CASE WHEN s.sex = "Female" THEN a.student_id END) as female
            ')
            ->get();

        // 6. Per-student table with penalty details
        // We use the Penalty model relationship to get student, program, and institute
        $penalties = Penalty::with(['student.user', 'student.program.institute'])
            ->where('event_id', $event->id)
            ->get();

        // Attendance grand totals (renamed from $grandTotal so that name is free for fines)
        $grandAttendance = $attendanceSummary->sum('total');
        $grandMale = $attendanceSummary->sum('male');
        $grandFemale = $attendanceSummary->sum('female');

        // ---- FINE SUMMARY (Support Collection) ----

        // Step 1: load this event's penalties with each student's program
        $fineRows = $event->penalties()->with('student.program')->get();

        $fineSummary = collect($fineRows)
            // Step 2: put penalties into groups, one group per program code
            ->groupBy(fn ($penalty) => $penalty->student->program->code)
            // Step 3: turn each group into ONE summary row
            ->map(fn ($group, $programCode) => [
                'program'  => $programCode,                                   // the program code
                'students' => $group->pluck('student_id')->unique()->count(), // how many different students
                'total'    => round((float) $group->sum('amount'), 2),        // sum of all fines in the group
            ])
            // Step 4: put the biggest fine totals first
            ->sortByDesc('total')
            // Step 5: reset the keys to 0, 1, 2... (groupBy left program codes as keys)
            ->values();

        // Step 6: add up every program's total to get the grand total of fines
        $grandTotal = $fineSummary->sum('total');

        return view('reports.show', compact(
            'event',
            'attendanceSummary',
            'penalties',
            'grandAttendance',
            'grandMale',
            'grandFemale',
            'fineSummary',
            'grandTotal'
        ));
    }
}