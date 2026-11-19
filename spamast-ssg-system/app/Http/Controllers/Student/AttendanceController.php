<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\EventSession;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request, EventSession $session)
    {
        $student = $request->user()->student;

        // 1. Ensure the user has a linked student record
        abort_unless($student, 403, 'Student profile not found.');

        // 2. Ensure the session is currently open for check-in
        abort_unless($session->isCheckInOpen(), 403, 'Check-in is currently closed.');

        // 3. Check if they already checked in
        $attendance = Attendance::firstOrCreate(
            [
                'event_session_id' => $session->id,
                'student_id' => $student->id,
            ],
            [
                'check_in_at' => now(),
            ]
        );

        // If the record already existed and had a check_in_at, show a specific message
        if (!$attendance->wasRecentlyCreated && $attendance->check_in_at) {
            return back()->with('error', 'You have already checked in for this session.');
        }

        // If it was just created (or somehow existed without a time), ensure the time is set
        $attendance->update(['check_in_at' => now()]);

        return back()->with('success', 'Check-in successful!');
    }

    public function checkOut(Request $request, EventSession $session)
    {
        $student = $request->user()->student;

        abort_unless($student, 403, 'Student profile not found.');
        abort_unless($session->isCheckOutOpen(), 403, 'Check-out is currently closed.');

        // Find the attendance record for this session that hasn't been checked out yet
        $attendance = Attendance::where('event_session_id', $session->id)
            ->where('student_id', $student->id)
            ->whereNull('check_out_at')
            ->firstOrFail();

        $attendance->update(['check_out_at' => now()]);

        return back()->with('success', 'Check-out successful!');
    }
}