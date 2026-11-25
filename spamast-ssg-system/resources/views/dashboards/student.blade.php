@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Student Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Upcoming Events</h3>
            <div class="number">{{ $stats['upcoming_events'] }}</div>
        </div>
        <div class="stat-box">
            <h3>My Attendance Records</h3>
            <div class="number">{{ $stats['my_attendance'] }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Student!</h2>
        <p>View upcoming campus events, check your attendance records, and stay updated.</p>
    </div>
@endsection