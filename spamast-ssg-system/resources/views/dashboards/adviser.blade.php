@extends('layouts.app')

@section('title', 'Adviser Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Adviser Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Upcoming Events</h3>
            <div class="number">{{ $stats['upcoming_events'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Attendance Records</h3>
            <div class="number">{{ $stats['attendance_records'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Unpaid Fines</h3>
            <div class="number">{{ $stats['unpaid_fines'] }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Adviser!</h2>
        <p>Review event proposals, monitor officer activities, and oversee the SSG operations.</p>
    </div>

    @include('partials.latest-announcements')
@endsection