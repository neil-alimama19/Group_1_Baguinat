@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 5px;">{{ $event->title }}</h1>
            <p style="color: #94a3b8;">{{ $event->venue }} | {{ $event->event_date->format('F d, Y') }}</p>
        </div>
        <a href="{{ route('events.index') }}" style="color: #d8b4fe; text-decoration: none; font-weight: bold;">&larr; Back to Events</a>
    </div>

    {{-- Show error/success messages --}}
    @if(session('error'))
        <div style="background: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">{{ session('success') }}</div>
    @endif

    {{-- Warning for students without a linked profile --}}
    @if(auth()->user()->hasRole('Student') && !auth()->user()->student)
        <div style="background: #f59e0b; color: #78350f; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
            Warning: Your user account is not linked to a student record. You cannot check in or out. Please contact an Officer.
        </div>
    @endif

    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="stat-box">
            <h3>Day Type</h3>
            <div class="number" style="font-size: 18px;">{{ Str::headline($event->day_type) }}</div>
        </div>
        <div class="stat-box">
            <h3>Availability</h3>
            <div class="number" style="font-size: 18px;">{{ Str::headline($event->availability) }}</div>
        </div>
        <div class="stat-box">
            <h3>Fine Per Miss</h3>
            <div class="number" style="font-size: 18px;">₱{{ number_format($event->fine_per_miss, 2) }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Description</h2>
        <p style="margin-top: 10px; color: #e2e8f0; line-height: 1.6;">{{ $event->description ?? 'No description provided.' }}</p>
    </div>

    {{-- MANAGEMENT & REPORT LINKS (Officers and Advisers) --}}
    @can('view-reports')
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
            <a href="{{ route('attendance.index', $event) }}" style="background: #0ea5e9; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">View Attendance</a>
            <a href="{{ route('penalties.index', $event) }}" style="background: #f59e0b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">View Penalties</a>
            <a href="{{ route('reports.show', $event) }}" style="background: #8b5cf6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">View Report</a>
        </div>
    @endcan

    {{-- OFFICER GENERATE PENALTIES ACTION --}}
    @can('manage-ssg')
        <div style="margin-bottom: 20px;">
            <form method="POST" action="{{ route('officer.penalties.generate', $event) }}" style="display: inline;" onsubmit="return confirm('Generate penalties for this event? This will calculate based on current attendance.');">
                @csrf
                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Generate Penalties</button>
            </form>
        </div>
    @endcan

    <div class="card">
        <h2>Sessions Schedule</h2>
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 10px;">Period</th>
                    <th style="padding: 10px;">Check-In Window</th>
                    <th style="padding: 10px;">Check-Out Window</th>
                    <th style="padding: 10px;">Status</th>
                    
                    {{-- Officer Controls Column --}}
                    @can('manage-ssg')
                        @if($event->availability === 'manual')
                            <th style="padding: 10px; text-align: right;">Actions</th>
                        @endif
                    @endcan

                    {{-- Student Controls Column --}}
                    @can('use-attendance')
                        <th style="padding: 10px; text-align: right;">My Attendance</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($event->sessions as $session)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 10px; font-weight: bold;">{{ Str::headline($session->period) }}</td>
                        <td style="padding: 10px;">
                            @if($session->check_in_start)
                                {{ \Carbon\Carbon::parse($session->check_in_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($session->check_in_end)->format('h:i A') }}
                            @else
                                Manual / Open
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            @if($session->check_out_start)
                                {{ \Carbon\Carbon::parse($session->check_out_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($session->check_out_end)->format('h:i A') }}
                            @else
                                Manual / Open
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            @if($session->isCheckInOpen())
                                <span style="color: #4ade80; font-weight: bold;">Check-In Open</span>
                            @else
                                <span style="color: #94a3b8;">Check-In Closed</span>
                            @endif
                            
                            <br>

                            @if($session->isCheckOutOpen())
                                <span style="color: #4ade80; font-weight: bold;">Check-Out Open</span>
                            @else
                                <span style="color: #94a3b8;">Check-Out Closed</span>
                            @endif
                        </td>
                        
                        {{-- OFFICER CONTROLS (Manual Toggles) --}}
                        @can('manage-ssg')
                            @if($event->availability === 'manual')
                                <td style="padding: 10px; text-align: right;">
                                    <form method="POST" action="{{ route('officer.sessions.toggle', $session) }}" style="display: inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="type" value="check_in">
                                        <button type="submit" style="background: {{ $session->check_in_open ? '#ef4444' : '#4ade80' }}; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; margin-bottom: 5px; display: block; width: 100%;">
                                            {{ $session->check_in_open ? 'Close Check-In' : 'Open Check-In' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('officer.sessions.toggle', $session) }}" style="display: inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="type" value="check_out">
                                        <button type="submit" style="background: {{ $session->check_out_open ? '#ef4444' : '#4ade80' }}; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; display: block; width: 100%;">
                                            {{ $session->check_out_open ? 'Close Check-Out' : 'Open Check-Out' }}
                                        </button>
                                    </form>
                                </td>
                            @endif
                        @endcan

                        {{-- STUDENT CONTROLS --}}
                        @can('use-attendance')
                            @php
                                $myAttendance = null;
                                if(auth()->user()->student) {
                                    $myAttendance = $session->attendances->where('student_id', auth()->user()->student->id)->first();
                                }
                            @endphp
                            <td style="padding: 10px; text-align: right;">
                                @if(auth()->user()->student)
                                    
                                    {{-- Display logged times --}}
                                    <div style="font-size: 12px; margin-bottom: 10px; color: #cbd5e1;">
                                        In: {{ $myAttendance && $myAttendance->check_in_at ? $myAttendance->check_in_at->format('h:i A') : '--' }}<br>
                                        Out: {{ $myAttendance && $myAttendance->check_out_at ? $myAttendance->check_out_at->format('h:i A') : '--' }}
                                    </div>

                                    {{-- Check In Button --}}
                                    @if($session->isCheckInOpen())
                                        @if(!$myAttendance || !$myAttendance->check_in_at)
                                            <form method="POST" action="{{ route('student.attendance.check-in', $session) }}">
                                                @csrf
                                                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; margin-bottom: 5px;">Check In</button>
                                            </form>
                                        @endif
                                    @endif

                                    {{-- Check Out Button --}}
                                    @if($session->isCheckOutOpen())
                                        @if($myAttendance && $myAttendance->check_in_at && !$myAttendance->check_out_at)
                                            <form method="POST" action="{{ route('student.attendance.check-out', $session) }}">
                                                @csrf
                                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%;">Check Out</button>
                                            </form>
                                        @endif
                                    @endif

                                @else
                                    <span style="font-size: 12px; color: #ef4444;">No student record</span>
                                @endif
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection