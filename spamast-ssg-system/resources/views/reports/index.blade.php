@extends('layouts.app')

@section('title', 'Event Reports')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Event Reports</h1>

    <div class="card">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Date</th>
                    <th style="padding: 12px;">Event Title</th>
                    <th style="padding: 12px;">Venue</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $event->event_date->format('M d, Y') }}</td>
                        <td style="padding: 12px; font-weight: bold;">{{ $event->title }}</td>
                        <td style="padding: 12px;">{{ $event->venue }}</td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('reports.show', $event) }}" style="background: #7c3aed; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold;">View Report</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center;">No events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $events->links() }}
        </div>
    </div>
@endsection