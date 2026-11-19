@extends('layouts.app')

@section('title', 'Officer Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Officer Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Events Managed</h3>
            <div class="number">3</div>
        </div>
        <div class="stat-box">
            <h3>Students Attended</h3>
            <div class="number">85</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Officer!</h2>
        <p>You can manage events, track attendance, and monitor student participation here.</p>
    </div>
@endsection