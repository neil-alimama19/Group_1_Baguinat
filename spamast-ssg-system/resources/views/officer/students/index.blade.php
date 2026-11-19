@extends('layouts.app')

@section('title', 'Manage Students')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #c084fc;">Manage Students</h1>
        <a href="{{ route('officer.students.create') }}" style="background: #7c3aed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Add Student</a>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('officer.students.index') }}" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Search by name or student no..." value="{{ request('search') }}" style="padding: 10px; border-radius: 8px; border: none; flex: 1; min-width: 200px;">
            
            <select name="institute_id" id="institute_filter" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Institutes</option>
                @foreach($institutes as $institute)
                    <option value="{{ $institute->id }}" {{ request('institute_id') == $institute->id ? 'selected' : '' }}>{{ $institute->code }}</option>
                @endforeach
            </select>

            <select name="program_id" id="program_filter" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Programs</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->code }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Filter</button>
            <a href="{{ route('officer.students.index') }}" style="padding: 10px 20px; color: #d8b4fe; text-decoration: none;">Clear</a>
        </form>

        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Student No</th>
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px;">Program</th>
                    <th style="padding: 12px;">Year</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $student->student_no }}</td>
                        <td style="padding: 12px;">{{ $student->user->first_name ?? $student->user->name }} {{ $student->user->last_name }}</td>
                        <td style="padding: 12px;">{{ $student->program->code ?? 'N/A' }} ({{ $student->program->institute->code ?? 'N/A' }})</td>
                        <td style="padding: 12px;">{{ $student->year_level }}</td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('officer.students.edit', $student) }}" style="color: #fbbf24; text-decoration: none; margin-right: 10px;">Edit</a>
                            <form method="POST" action="{{ route('officer.students.destroy', $student) }}" style="display: inline;" onsubmit="return confirm('Delete this student and their user account?');">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: transparent; border: none; color: #ef4444; cursor: pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center;">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $students->links() }}
        </div>
    </div>

    <script>
        // Filter programs based on selected institute
        document.getElementById('institute_filter').addEventListener('change', function() {
            const instituteId = this.value;
            const programSelect = document.getElementById('program_filter');
            const options = programSelect.querySelectorAll('option');

            options.forEach(option => {
                if (!option.value) return; // Skip the "All Programs" option
                
                if (!instituteId || option.getAttribute('data-institute') === instituteId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Reset program selection if it's no longer visible
            if (programSelect.options[programSelect.selectedIndex].style.display === 'none') {
                programSelect.value = '';
            }
        });
    </script>
@endsection