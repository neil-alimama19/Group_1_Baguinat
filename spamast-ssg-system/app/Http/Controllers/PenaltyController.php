<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Penalty;
use App\Models\Institute;
use App\Models\Program;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $query = Penalty::with(['student.user', 'student.program.institute'])
            ->where('event_id', $event->id);

        // Filter by Institute
        if ($request->filled('institute_id')) {
            $instituteId = $request->input('institute_id');
            $query->whereHas('student.program', function ($q) use ($instituteId) {
                $q->where('institute_id', $instituteId);
            });
        }

        // Filter by Program
        if ($request->filled('program_id')) {
            $programId = $request->input('program_id');
            $query->whereHas('student', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $penalties = $query->paginate(20)->withQueryString();
        $totalAmount = $query->sum('amount');

        $institutes = Institute::all();
        $programs = Program::all();

        return view('penalties.index', compact('event', 'penalties', 'totalAmount', 'institutes', 'programs'));
    }
}