@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Admin Dashboard</h1>
    
    <div class="stats-grid">
        {{-- One box per role: Admin, Officer, Adviser, Student --}}
        @foreach($stats['users_per_role'] as $role => $count)
            <div class="stat-box">
                <h3>{{ $role }} Accounts</h3>
                <div class="number">{{ $count }}</div>
            </div>
        @endforeach
        <div class="stat-box">
            <h3>Pending Approvals</h3>
            <div class="number">{{ $stats['pending_approvals'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Institutes</h3>
            <div class="number">{{ $stats['institutes'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Programs</h3>
            <div class="number">{{ $stats['programs'] }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Admin!</h2>
        <p>From here you can manage all users, oversee all events, and control system settings.</p>
    </div>
@endsection