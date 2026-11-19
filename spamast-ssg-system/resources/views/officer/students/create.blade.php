@extends('layouts.app')

@section('title', 'Add Student')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Add New Student</h1>

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

        <form method="POST" action="{{ route('officer.students.store') }}">
            @csrf
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Institute</label>
                    <select id="institute_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                        <option value="">Select Institute First</option>
                        @foreach($institutes as $institute)
                            <option value="{{ $institute->id }}">{{ $institute->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Program</label>
                    <select name="program_id" id="program_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                        <option value="">Select Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Student No</label>
                    <input type="text" name="student_no" value="{{ old('student_no') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Sex</label>
                    <select name="sex" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px;">Year Level</label>
                    <input type="number" name="year_level" value="{{ old('year_level', 1) }}" min="1" max="5" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
            </div>

            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer;">Save Student</button>
            <a href="{{ route('officer.students.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none;">Cancel</a>
        </form>
    </div>

    <script>
        // Dynamic filtering for Create Form
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
        
        // Trigger on load in case of old() values
        if (document.getElementById('institute_id').value) {
            document.getElementById('institute_id').dispatchEvent(new Event('change'));
        }
    </script>
@endsection