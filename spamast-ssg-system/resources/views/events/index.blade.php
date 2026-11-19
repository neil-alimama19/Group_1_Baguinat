@extends('layouts.app')

@section('title', 'Events')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #c084fc;">Events</h1>
        @can('manage-ssg')
            <a href="{{ route('officer.events.create') }}" style="background: #7c3aed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Create Event</a>
        @endcan
    </div>

    <div class="card">
        {{-- Tabs --}}
        <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
            <a href="{{ route('events.index', ['tab' => 'upcoming']) }}" 
               style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; 
                      background: {{ request('tab', 'upcoming') === 'upcoming' ? '#7c3aed' : 'transparent' }}; 
                      color: white;">
                Upcoming
            </a>
            <a href="{{ route('events.index', ['tab' => 'past']) }}" 
               style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; 
                      background: {{ request('tab') === 'past' ? '#7c3aed' : 'transparent' }}; 
                      color: {{ request('tab') === 'past' ? 'white' : '#d8b4fe' }};">
                Past
            </a>
        </div>

        <form method="GET" action="{{ route('events.index') }}" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="hidden" name="tab" value="{{ request('tab', 'upcoming') }}">
            <input type="text" name="search" placeholder="Search events by title..." value="{{ request('search') }}" style="padding: 10px; border-radius: 8px; border: none; flex: 1;">
            <input type="date" name="date" value="{{ request('date') }}" style="padding: 10px; border-radius: 8px; border: none;">
            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Search</button>
            @if(request('search') || request('date'))
                <a href="{{ route('events.index', ['tab' => request('tab', 'upcoming')]) }}" style="padding: 10px 20px; color: #d8b4fe; text-decoration: none;">Clear</a>
            @endif
        </form>

        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Date</th>
                    <th style="padding: 12px;">Title</th>
                    <th style="padding: 12px;">Venue</th>
                    <th style="padding: 12px;">Type</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $event->event_date->format('M d, Y') }}</td>
                        <td style="padding: 12px; font-weight: bold;">{{ $event->title }}</td>
                        <td style="padding: 12px;">{{ $event->venue }}</td>
                        <td style="padding: 12px;">
                            <span style="padding: 4px 8px; border-radius: 4px; background: rgba(255,255,255,0.1);">
                                {{ ucfirst(str_replace('_', ' ', $event->day_type)) }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('events.show', $event) }}" style="color: #60a5fa; text-decoration: none; margin-right: 10px;">View</a>
                            
                            @can('manage-ssg')
                                <a href="{{ route('officer.events.edit', $event) }}" style="color: #fbbf24; text-decoration: none; margin-right: 10px;">Edit</a>
                                <form method="POST" action="{{ route('officer.events.destroy', $event) }}" style="display: inline;" onsubmit="return confirm('Delete this event?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: transparent; border: none; color: #ef4444; cursor: pointer;">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center;">No events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $events->links() }}
        </div>
    </div>
@endsection