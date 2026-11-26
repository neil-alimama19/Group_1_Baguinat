<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('creator')->withCount('attendances');

        // Search by title if provided
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        // Upcoming / Past tabs. Anything that is not "past" is treated as "upcoming" (the default tab).
        $isPast = $request->input('tab') === 'past';

        if ($isPast) {
            // Past: events dated before today
            $query->whereDate('event_date', '<', today());
        } else {
            // Upcoming: events dated today or later (today() is midnight, so today's events stay here)
            $query->whereDate('event_date', '>=', today());
        }

        // Date search: narrow the current tab to one exact date
        if ($request->filled('date')) {
            $query->whereDate('event_date', $request->input('date'));
        }

        // Upcoming: soonest first. Past: most recent first.
        $events = $query->orderBy('event_date', $isPast ? 'desc' : 'asc')
            ->paginate(10)
            ->withQueryString(); // keeps tab, search and date when changing pages

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        // Load relationships needed for the view
        $event->load(['sessions', 'creator', 'penalties']);

        return view('events.show', compact('event'));
    }
}