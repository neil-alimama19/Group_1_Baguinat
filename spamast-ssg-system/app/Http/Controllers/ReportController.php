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

        // Calculate Grand Totals from the summary
        $grandTotal = $attendanceSummary->sum('total');
        $grandMale = $attendanceSummary->sum('male');
        $grandFemale = $attendanceSummary->sum('female');

        return view('reports.show', compact(
            'event', 
            'attendanceSummary', 
            'penalties',
            'grandTotal',
            'grandMale',
            'grandFemale'
        ));
    }
}