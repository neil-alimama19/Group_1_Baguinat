@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Total Users</h3>
            <div class="number">120</div>
        </div>
        <div class="stat-box">
            <h3>Active Events</h3>
            <div class="number">5</div>
        </div>
        <div class="stat-box">
            <h3>Sanctions Issued</h3>
            <div class="number">12</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Admin!</h2>
        <p>From here you can manage all users, oversee all events, and control system settings.</p>
    </div>
@endsection