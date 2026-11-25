<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function create()
    {
        return view('officer.events.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        DB::transaction(function () use ($request, $validated) {
            $event = Event::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'venue' => $validated['venue'],
                'event_date' => $validated['event_date'],
                'day_type' => $validated['day_type'],
                'availability' => $validated['availability'],
                'fine_per_miss' => $validated['fine_per_miss'],
                'created_by' => $request->user()->id,
            ]);

            $this->saveSessions($event, $validated);
        });

        $this->forgetDashboardCache(); // clear outdated dashboard numbers

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        Gate::authorize('update', $event);
        
        // Load sessions so we can populate the form
        $event->load('sessions');
        $sessions = $event->sessions->keyBy('period'); // Group by period for easy access in view

        return view('officer.events.edit', compact('event', 'sessions'));
    }

    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);

        $validated = $this->validateEvent($request);

        DB::transaction(function () use ($request, $validated, $event) {
            $oldDayType = $event->day_type;

            $event->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'venue' => $validated['venue'],
                'event_date' => $validated['event_date'],
                'day_type' => $validated['day_type'],
                'availability' => $validated['availability'],
                'fine_per_miss' => $validated['fine_per_miss'],
            ]);

            // If the day_type changed, we must remove the sessions that no longer apply
            // ONLY IF they have no attendances yet (to prevent data loss).
            if ($oldDayType !== $validated['day_type']) {
                $sessionsToRemove = $oldDayType === 'whole_day' ? ['morning', 'afternoon'] : ['half_day'];
                
                foreach ($sessionsToRemove as $period) {
                    $session = $event->sessions()->where('period', $period)->first();
                    
                    if ($session) {
                        // Only delete if no student has checked in yet
                        if ($session->attendances()->count() === 0) {
                            $session->delete();
                        } else {
                            // If there is attendance, we cannot change the day type safely
                            abort(403, 'Cannot change day type because students have already recorded attendance for the old sessions.');
                        }
                    }
                }
            }

            // Now save the new sessions
            $this->saveSessions($event, $validated);
        });

        $this->forgetDashboardCache(); // clear outdated dashboard numbers

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $event->delete();

        $this->forgetDashboardCache(); // clear outdated dashboard numbers

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    // Event changes affect the Officer, the Adviser and every Student dashboard
    private function forgetDashboardCache(): void
    {
        Cache::forget('officer.stats');
        Cache::forget('adviser.stats');

        // Student keys are per user, so forget each student's own key
        Student::pluck('user_id')->each(fn ($userId) => Cache::forget("student.stats.{$userId}"));
    }

    private function validateEvent(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'event_date' => 'required|date',
            'day_type' => 'required|in:whole_day,half_day',
            'availability' => 'required|in:scheduled,manual',
            'fine_per_miss' => 'required|numeric|min:0',
        ];

        // Only validate times if availability is scheduled
        if ($request->input('availability') === 'scheduled') {
            if ($request->input('day_type') === 'whole_day') {
                $rules = array_merge($rules, [
                    'sessions.morning.check_in_start' => 'required|date_format:H:i',
                    'sessions.morning.check_in_end' => 'required|date_format:H:i|after:sessions.morning.check_in_start',
                    'sessions.morning.check_out_start' => 'required|date_format:H:i',
                    'sessions.morning.check_out_end' => 'required|date_format:H:i|after:sessions.morning.check_out_start',
                    'sessions.afternoon.check_in_start' => 'required|date_format:H:i',
                    'sessions.afternoon.check_in_end' => 'required|date_format:H:i|after:sessions.afternoon.check_in_start',
                    'sessions.afternoon.check_out_start' => 'required|date_format:H:i',
                    'sessions.afternoon.check_out_end' => 'required|date_format:H:i|after:sessions.afternoon.check_out_start',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'sessions.half_day.check_in_start' => 'required|date_format:H:i',
                    'sessions.half_day.check_in_end' => 'required|date_format:H:i|after:sessions.half_day.check_in_start',
                    'sessions.half_day.check_out_start' => 'required|date_format:H:i',
                    'sessions.half_day.check_out_end' => 'required|date_format:H:i|after:sessions.half_day.check_out_start',
                ]);
            }
        }

        return $request->validate($rules);
    }

    private function saveSessions(Event $event, array $validated)
    {
        $periods = $validated['day_type'] === 'whole_day' ? ['morning', 'afternoon'] : ['half_day'];

        foreach ($periods as $period) {
            $sessionData = $validated['sessions'][$period] ?? [];
            
            $event->sessions()->updateOrCreate(
                ['period' => $period],
                [
                    'check_in_start' => $sessionData['check_in_start'] ?? null,
                    'check_in_end' => $sessionData['check_in_end'] ?? null,
                    'check_out_start' => $sessionData['check_out_start'] ?? null,
                    'check_out_end' => $sessionData['check_out_end'] ?? null,
                ]
            );
        }
    }
}