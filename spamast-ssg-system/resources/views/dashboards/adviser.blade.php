@extends('layouts.app')

@section('title', 'Adviser Dashboard')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Adviser Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Pending Approvals</h3>
            <div class="number">2</div>
        </div>
        <div class="stat-box">
            <h3>System Reports</h3>
            <div class="number">8</div>
        </div>
    </div>

    <div class="card">
        <h2>Welcome, Adviser!</h2>
        <p>Review event proposals, monitor officer activities, and oversee the SSG operations.</p>
    </div>
@endsection