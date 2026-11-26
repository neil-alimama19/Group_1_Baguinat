@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 28px; color: #c084fc;">Attendance: {{ $event->title }}</h1>
            <p style="color: #94a3b8;">{{ $event->event_date->format('F d, Y') }}</p>
        </div>
        <a href="{{ route('events.show', $event) }}" style="color: #d8b4fe; text-decoration: none; font-weight: bold;">&larr; Back to Event</a>
    </div>

    {{-- Success/error alerts are now shown by layouts/app.blade.php --}}

    <div class="card">
        <form method="GET" action="{{ route('attendance.index', $event) }}" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
            
            <select name="session_id" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Sessions</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>{{ Str::headline($session->period) }}</option>
                @endforeach
            </select>

            <select name="institute_id" id="institute_filter" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Institutes</option>
                @foreach($institutes as $institute)
                    <option value="{{ $institute->id }}" {{ request('institute_id') == $institute->id ? 'selected' : '' }}>{{ $institute->code }}</option>
                @endforeach
            </select>

            <select name="program_id" id="program_filter" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Programs</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->code }}</option>
                @endforeach
            </select>
            
            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Filter</button>
            {{-- clear=1 tells the controller to remove filters.institute_id and filters.program_id from the session --}}
            <a href="{{ route('attendance.index', [$event, 'clear' => 1]) }}" style="padding: 10px 20px; color: #d8b4fe; text-decoration: none;">Clear Filters</a>
        </form>

        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Student No</th>
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px;">Program (Institute)</th>
                    <th style="padding: 12px;">Session</th>
                    <th style="padding: 12px;">Check In</th>
                    <th style="padding: 12px;">Check Out</th>
                    @can('manage-ssg')
                        <th style="padding: 12px; text-align: right;">Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $attendance->student->student_no }}</td>
                        <td style="padding: 12px;">{{ $attendance->student->user->full_name }}</td>
                        <td style="padding: 12px;">{{ $attendance->student->program->code }} ({{ $attendance->student->program->institute->code }})</td>
                        <td style="padding: 12px;">{{ Str::headline($attendance->session->period) }}</td>
                        <td style="padding: 12px;">{{ $attendance->check_in_at ? $attendance->check_in_at->format('h:i A') : '--' }}</td>
                        <td style="padding: 12px;">{{ $attendance->check_out_at ? $attendance->check_out_at->format('h:i A') : '--' }}</td>
                        
                        @can('manage-ssg')
                            <td style="padding: 12px; text-align: right; display: flex; gap: 5px; justify-content: flex-end;">
                                <button type="button" onclick="openEditModal({{ $attendance->id }}, '{{ $attendance->check_in_at ? $attendance->check_in_at->format('Y-m-d\TH:i') : '' }}', '{{ $attendance->check_out_at ? $attendance->check_out_at->format('Y-m-d\TH:i') : '' }}')" style="background: #fbbf24; color: black; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px;">Edit</button>
                                <form method="POST" action="{{ route('officer.attendance.destroy', $attendance) }}" onsubmit="return confirm('Delete this record?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px;">Delete</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center;">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $attendances->links() }}
        </div>
    </div>

    {{-- ADD RECORD MODAL / FORM (Officer Only) --}}
    @can('manage-ssg')
        <div class="card" style="margin-top: 30px; max-width: 600px;">
            <h2>Add Missing Attendance Record</h2>
            <form method="POST" action="{{ route('officer.attendance.store', $event) }}" style="margin-top: 15px;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px;">Student</label>
                        <select name="student_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                            <option value="">Select Student</option>
                            @foreach(\App\Models\Student::with('user')->get() as $student)
                                <option value="{{ $student->id }}">{{ $student->student_no }} - {{ $student->user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px;">Session</label>
                        <select name="event_session_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}">{{ Str::headline($session->period) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px;">Check In Time</label>
                        <input type="datetime-local" name="check_in_at" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px;">Check Out Time</label>
                        <input type="datetime-local" name="check_out_at" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                    </div>
                </div>
                <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%;">Add Record</button>
            </form>
        </div>

        {{-- EDIT RECORD MODAL --}}
        <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; z-index: 1000;">
            <div class="card" style="width: 400px; background: #1e1b4b;">
                <h2>Edit Times</h2>
                <form id="editForm" method="POST" style="margin-top: 15px;">
                    @csrf 
                    @method('PUT')
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Check In Time</label>
                        <input type="datetime-local" name="check_in_at" id="edit_check_in" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Check Out Time</label>
                        <input type="datetime-local" name="check_out_at" id="edit_check_out" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" style="background: #4ade80; color: black; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;">Save Changes</button>
                        <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // CHANGED: the address comes from the named route officer.attendance.update (PUT /officer/attendance/{attendance}).
            // ':id' is a placeholder, and 'false' keeps the URL relative (no domain).
            const attendanceUrlTemplate = @js(route('officer.attendance.update', ':id', false));

            function openEditModal(id, checkIn, checkOut) {
                // Swap the placeholder for the real attendance id
                document.getElementById('editForm').action = attendanceUrlTemplate.replace(':id', id);
                document.getElementById('edit_check_in').value = checkIn;
                document.getElementById('edit_check_out').value = checkOut;
                document.getElementById('editModal').style.display = 'flex';
            }
        </script>
    @endcan

    {{-- JavaScript for Institute/Program Filter --}}
    <script>
        const instituteSelect = document.getElementById('institute_filter');
        const programSelect = document.getElementById('program_filter');

        // Show only the programs of the chosen institute.
        // resetProgram = true only when the user changes the institute (not on page load).
        function filterPrograms(resetProgram) {
            const instituteId = instituteSelect.value;

            if (resetProgram) {
                programSelect.value = ''; // Reset selection
            }

            programSelect.querySelectorAll('option').forEach(option => {
                if (!option.value) return;
                if (!instituteId || option.getAttribute('data-institute') === instituteId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        // User picks a new institute -> reset the program
        instituteSelect.addEventListener('change', () => filterPrograms(true));

        // On page load (including restored session filters) -> keep the selected program
        filterPrograms(false);
    </script>
@endsection