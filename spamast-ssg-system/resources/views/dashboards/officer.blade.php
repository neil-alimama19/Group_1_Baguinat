@extends('layouts.app')

@section('title', 'Officer Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Officer Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Students</h3>
            <div class="number">{{ $stats['students'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Upcoming Events</h3>
            <div class="number">{{ $stats['upcoming_events'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Unpaid Fines</h3>
            <div class="number">{{ $stats['unpaid_fines'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Announcements</h3>
            <div class="number">{{ $stats['announcements'] }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Officer!</h2>
        <p>You can manage events, track attendance, and monitor student participation here.</p>
    </div>
@endsection