@extends('layouts.app')

@section('title', 'Report: ' . $event->title)

@section('content')
    {{-- Print-friendly CSS --}}
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .sidebar, .top-navbar, .no-print { display: none !important; }
            .main-wrapper, .content { padding: 0 !important; margin: 0 !important; width: 100% !important; }
            .card { background: white !important; border: 1px solid #ccc !important; color: black !important; }
            .stat-box { background: #f3f4f6 !important; border: 1px solid #ccc !important; color: black !important; }
            .stat-box h3, .stat-box .number, h1, h2, table { color: black !important; }
            table th, table td { border-bottom: 1px solid #ccc !important; }
        }
    </style>

    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 5px;">Report: {{ $event->title }}</h1>
            <p style="color: #94a3b8;">{{ $event->venue }} | {{ $event->event_date->format('F d, Y') }}</p>
        </div>
        <div style="display: flex; gap: 15px;">
            <button onclick="window.print()" style="background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Print Report</button>
            <a href="{{ route('reports.index') }}" style="color: #d8b4fe; text-decoration: none; font-weight: bold; align-self: center;">&larr; Back to Reports</a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
        <div class="stat-box">
            <h3>Total Attendees</h3>
            {{-- CHANGED: was $grandTotal, now $grandAttendance --}}
            <div class="number">{{ $grandAttendance }}</div>
        </div>
        <div class="stat-box">
            <h3>Male Attendees</h3>
            <div class="number">{{ $grandMale }}</div>
        </div>
        <div class="stat-box">
            <h3>Female Attendees</h3>
            <div class="number">{{ $grandFemale }}</div>
        </div>
        <div class="stat-box">
            <h3>Total Penalties</h3>
            <div class="number">₱{{ number_format($penalties->sum('amount'), 2) }}</div>
        </div>
    </div>

    {{-- Summary Table (Per Institute/Program) --}}
    <div class="card">
        <h2>Attendance Summary</h2>
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 10px;">Institute</th>
                    <th style="padding: 10px;">Program</th>
                    <th style="padding: 10px;">Total Attendees</th>
                    <th style="padding: 10px;">Male</th>
                    <th style="padding: 10px;">Female</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceSummary as $row)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 10px; font-weight: bold;">{{ $row->institute_code }}</td>
                        <td style="padding: 10px;">{{ $row->program_code }}</td>
                        <td style="padding: 10px;">{{ $row->total }}</td>
                        <td style="padding: 10px;">{{ $row->male }}</td>
                        <td style="padding: 10px;">{{ $row->female }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center;">No attendance recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid rgba(255,255,255,0.3); font-weight: bold;">
                    <td colspan="2" style="padding: 10px; text-align: right;">GRAND TOTALS:</td>
                    {{-- CHANGED: was $grandTotal, now $grandAttendance --}}
                    <td style="padding: 10px;">{{ $grandAttendance }}</td>
                    <td style="padding: 10px;">{{ $grandMale }}</td>
                    <td style="padding: 10px;">{{ $grandFemale }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- NEW: Fine Summary (per program) --}}
    <div class="card">
        <h2>Fine Summary</h2>
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 10px;">Program</th>
                    <th style="padding: 10px;">Students</th>
                    <th style="padding: 10px;">Total Fines</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fineSummary as $row)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        {{-- Each $row is an array built by map() in the controller --}}
                        <td style="padding: 10px; font-weight: bold;">{{ $row['program'] }}</td>
                        <td style="padding: 10px;">{{ $row['students'] }}</td>
                        <td style="padding: 10px;">₱{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 20px; text-align: center;">No fines generated for this event.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid rgba(255,255,255,0.3); font-weight: bold;">
                    <td colspan="2" style="padding: 10px; text-align: right;">GRAND TOTAL:</td>
                    {{-- $grandTotal is now the sum of all fines --}}
                    <td style="padding: 10px;">₱{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Per-Student Penalties --}}
    <div class="card">
        <h2>Student Penalties</h2>
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 10px;">Student No</th>
                    <th style="padding: 10px;">Name</th>
                    <th style="padding: 10px;">Program</th>
                    <th style="padding: 10px;">Missed Scans</th>
                    <th style="padding: 10px;">Amount</th>
                    <th style="padding: 10px;">Sanction</th>
                    <th style="padding: 10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penalties as $penalty)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 10px;">{{ $penalty->student->student_no }}</td>
                        <td style="padding: 10px;">{{ $penalty->student->user->full_name }}</td>
                        <td style="padding: 10px;">{{ $penalty->student->program->code }}</td>
                        <td style="padding: 10px; font-weight: bold;">{{ $penalty->missed_scans }}</td>
                        <td style="padding: 10px;">₱{{ number_format($penalty->amount, 2) }}</td>
                        <td style="padding: 10px;">{{ $penalty->sanction ?? '--' }}</td>
                        <td style="padding: 10px;">{{ Str::headline($penalty->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center;">No penalties generated for this event.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection