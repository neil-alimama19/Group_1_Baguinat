<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Penalty;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PenaltyController extends Controller
{
    public function generate(Event $event)
    {
        // 1. Load event sessions with their attendances
        $event->load('sessions.attendances');
        
        $sessionsCount = $event->sessions->count();
        $requiredScans = $sessionsCount * 2;

        // 2. Iterate through every student in the system
        // (In a real-world scenario, you might only want students from specific programs, but the prompt implies all)
        $students = Student::all();

        foreach ($students as $student) {
            $countedScans = 0;

            // Count non-null check-ins and check-outs for this student across all sessions
            foreach ($event->sessions as $session) {
                $attendance = $session->attendances->where('student_id', $student->id)->first();
                
                if ($attendance) {
                    if ($attendance->check_in_at) $countedScans++;
                    if ($attendance->check_out_at) $countedScans++;
                }
            }

            $missedScans = max(0, $requiredScans - $countedScans);

            if ($missedScans === 0) {
                // If no missed scans, remove any existing penalty for this student
                Penalty::where('event_id', $event->id)
                    ->where('student_id', $student->id)
                    ->delete();
            } else {
                // Create or update the penalty
                // We do not overwrite 'sanction' or 'status' if they already exist
                Penalty::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'missed_scans' => $missedScans,
                        'amount' => $missedScans * $event->fine_per_miss,
                    ]
                );
            }
        }

        $this->forgetDashboardCache(); // clear outdated dashboard numbers

        return redirect()->route('penalties.index', $event)->with('success', 'Penalties generated successfully.');
    }

    public function update(Request $request, Penalty $penalty)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'sanction' => 'nullable|string|max:255',
            'status' => 'required|in:unpaid,paid,served',
        ]);

        $penalty->update($validated);

        $this->forgetDashboardCache(); // clear outdated dashboard numbers

        // Go to this event's fines page (the feature's index) with a success message
        return redirect()->route('penalties.index', $penalty->event_id)->with('success', 'Penalty updated successfully.');
    }

    public function destroy(Penalty $penalty)
    {
        $penalty->delete();

        $this->forgetDashboardCache(); // clear outdated dashboard numbers

        // The model still holds event_id after delete(), so we can build the route
        return redirect()->route('penalties.index', $penalty->event_id)->with('success', 'Penalty deleted successfully.');
    }

    // Fines changed: the Officer and Adviser dashboards show unpaid fines
    private function forgetDashboardCache(): void
    {
        Cache::forget('officer.stats');
        Cache::forget('adviser.stats');
    }
}