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

        $events = $query->orderByDesc('event_date')->paginate(10)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        // Load relationships needed for the view
        $event->load(['sessions', 'creator', 'penalties']);

        return view('events.show', compact('event'));
    }
}