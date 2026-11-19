@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Edit Student</h1>

    <div class="card" style="max-width: 700px;">
        @if ($errors->any())
            <div style="background: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('officer.students.update', $student) }}">
            @csrf
            @method('PUT')
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $student->user->first_name) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $student->user->last_name) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" value="{{ old('email', $student->user->email) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Institute</label>
                    <select id="institute_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                        <option value="">Select Institute First</option>
                        @foreach($institutes as $institute)
                            <option value="{{ $institute->id }}" {{ $student->program->institute_id == $institute->id ? 'selected' : '' }}>{{ $institute->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Program</label>
                    <select name="program_id" id="program_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                        <option value="">Select Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}" {{ $student->program_id == $program->id ? 'selected' : '' }}>
                                {{ $program->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Student No</label>
                    <input type="text" name="student_no" value="{{ old('student_no', $student->student_no) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Sex</label>
                    <select name="sex" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                        <option value="Male" {{ $student->sex == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $student->sex == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Year Level</label>
                    <input type="number" name="year_level" value="{{ old('year_level', $student->year_level) }}" min="1" max="5" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
            </div>

            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer;">Update Student</button>
            <a href="{{ route('officer.students.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none;">Cancel</a>
        </form>
    </div>

    <script>
        // Dynamic filtering for Edit Form
        document.getElementById('institute_id').addEventListener('change', function() {
            const instituteId = this.value;
            const programSelect = document.getElementById('program_id');
            const options = programSelect.querySelectorAll('option');

            programSelect.value = ''; // Reset selection

            options.forEach(option => {
                if (!option.value) return; 
                if (!instituteId || option.getAttribute('data-institute') === instituteId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
        
        // Trigger on load to filter out unavailable programs
        document.getElementById('institute_id').dispatchEvent(new Event('change'));
        
        // Re-select the current program if it matches
        const currentProgramId = "{{ old('program_id', $student->program_id) }}";
        if (currentProgramId) {
            document.getElementById('program_id').value = currentProgramId;
        }
    </script>
@endsection