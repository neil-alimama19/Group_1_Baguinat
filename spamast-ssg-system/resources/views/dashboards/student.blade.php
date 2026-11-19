@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Student Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Events Joined</h3>
            <div class="number">4</div>
        </div>
        <div class="stat-box">
            <h3>Attendance Streak</h3>
            <div class="number">100%</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Student!</h2>
        <p>View upcoming campus events, check your attendance records, and stay updated.</p>
    </div>
@endsection