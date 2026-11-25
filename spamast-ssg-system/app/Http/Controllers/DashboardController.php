<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Institute;
use App\Models\Penalty;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function admin()
    {
        // Cached for 10 minutes. Only plain numbers/arrays are stored, never Eloquent models.
        $stats = Cache::remember('admin.stats', now()->addMinutes(10), function () {
            return [
                // e.g. ['Admin' => 1, 'Officer' => 2, 'Adviser' => 1, 'Student' => 20]
                'users_per_role'    => Role::withCount('users')->get()->pluck('users_count', 'name')->all(),
                'pending_approvals' => User::where('is_active', false)->count(), // awaiting Admin approval
                'institutes'        => Institute::count(),
                'programs'          => Program::count(),
            ];
        });

        return view('dashboards.admin', compact('stats'));
    }

    public function officer()
    {
        $stats = Cache::remember('officer.stats', now()->addMinutes(10), function () {
            return [
                'students'        => Student::count(),
                'upcoming_events' => $this->upcomingEvents(),
                'unpaid_fines'    => Penalty::where('status', 'unpaid')->count(),
                'announcements'   => Announcement::count(),
            ];
        });

        $announcements = $this->latestAnnouncements();

        return view('dashboards.officer', compact('stats', 'announcements'));
    }

    public function adviser()
    {
        $stats = Cache::remember('adviser.stats', now()->addMinutes(10), function () {
            return [
                'upcoming_events'    => $this->upcomingEvents(),
                'attendance_records' => Attendance::count(),
                'unpaid_fines'       => Penalty::where('status', 'unpaid')->count(),
            ];
        });

        $announcements = $this->latestAnnouncements();

        return view('dashboards.adviser', compact('stats', 'announcements'));
    }

    public function student(Request $request)
    {
        $user = $request->user();

        // One cache key per student, because the attendance count is personal
        $stats = Cache::remember("student.stats.{$user->id}", now()->addMinutes(10), function () use ($user) {
            $student = $user->student; // can be null if the account has no student profile

            return [
                'upcoming_events' => $this->upcomingEvents(),
                'my_attendance'   => $student ? Attendance::where('student_id', $student->id)->count() : 0,
            ];
        });

        $announcements = $this->latestAnnouncements();

        return view('dashboards.student', compact('stats', 'announcements'));
    }

    // Events dated today or later (today() is midnight, so today's events still count)
    private function upcomingEvents(): int
    {
        return Event::whereDate('event_date', '>=', today())->count();
    }

    // The 3 newest announcements. Not cached, so a new post shows immediately.
    private function latestAnnouncements()
    {
        return Announcement::latest()->take(3)->get();
    }
}