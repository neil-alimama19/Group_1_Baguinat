@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Edit Event: {{ $event->title }}</h1>

    <div class="card" style="max-width: 800px;">
        @if ($errors->any())
            <div style="background: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('officer.events.update', $event) }}">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">Title</label>
                    <input type="text" name="title" value="{{ old('title', $event->title) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Description (Optional)</label>
                <textarea name="description" style="width: 100%; padding: 10px; border-radius: 8px; border: none; height: 80px;">{{ old('description', $event->description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">Event Date</label>
                    <input type="date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">Day Type</label>
                    <select name="day_type" id="day_type" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                        <option value="whole_day" {{ old('day_type', $event->day_type) == 'whole_day' ? 'selected' : '' }}>Whole Day</option>
                        <option value="half_day" {{ old('day_type', $event->day_type) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">Availability</label>
                    <select name="availability" id="availability" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                        <option value="scheduled" {{ old('availability', $event->availability) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="manual" {{ old('availability', $event->availability) == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 5px;">Fine Per Missed Scan (₱)</label>
                <input type="number" name="fine_per_miss" value="{{ old('fine_per_miss', $event->fine_per_miss) }}" min="0" step="0.01" style="width: 200px; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div id="sessions_container" style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px; margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px; color: #e9d5ff;">Session Times</h3>
                
                <!-- Morning Session -->
                @php $morning = $sessions->get('morning'); @endphp
                <div id="morning_session" class="session-block" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px;">Morning</h4>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                        <div><label style="font-size: 12px;">In Start</label><input type="time" name="sessions[morning][check_in_start]" value="{{ old('sessions.morning.check_in_start', $morning?->check_in_start ? \Carbon\Carbon::parse($morning->check_in_start)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">In End</label><input type="time" name="sessions[morning][check_in_end]" value="{{ old('sessions.morning.check_in_end', $morning?->check_in_end ? \Carbon\Carbon::parse($morning->check_in_end)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">Out Start</label><input type="time" name="sessions[morning][check_out_start]" value="{{ old('sessions.morning.check_out_start', $morning?->check_out_start ? \Carbon\Carbon::parse($morning->check_out_start)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">Out End</label><input type="time" name="sessions[morning][check_out_end]" value="{{ old('sessions.morning.check_out_end', $morning?->check_out_end ? \Carbon\Carbon::parse($morning->check_out_end)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                    </div>
                </div>

                <!-- Afternoon Session -->
                @php $afternoon = $sessions->get('afternoon'); @endphp
                <div id="afternoon_session" class="session-block" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px;">Afternoon</h4>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                        <div><label style="font-size: 12px;">In Start</label><input type="time" name="sessions[afternoon][check_in_start]" value="{{ old('sessions.afternoon.check_in_start', $afternoon?->check_in_start ? \Carbon\Carbon::parse($afternoon->check_in_start)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">In End</label><input type="time" name="sessions[afternoon][check_in_end]" value="{{ old('sessions.afternoon.check_in_end', $afternoon?->check_in_end ? \Carbon\Carbon::parse($afternoon->check_in_end)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">Out Start</label><input type="time" name="sessions[afternoon][check_out_start]" value="{{ old('sessions.afternoon.check_out_start', $afternoon?->check_out_start ? \Carbon\Carbon::parse($afternoon->check_out_start)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">Out End</label><input type="time" name="sessions[afternoon][check_out_end]" value="{{ old('sessions.afternoon.check_out_end', $afternoon?->check_out_end ? \Carbon\Carbon::parse($afternoon->check_out_end)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                    </div>
                </div>

                <!-- Half Day Session -->
                @php $halfDay = $sessions->get('half_day'); @endphp
                <div id="half_day_session" class="session-block" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px;">Half Day</h4>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                        <div><label style="font-size: 12px;">In Start</label><input type="time" name="sessions[half_day][check_in_start]" value="{{ old('sessions.half_day.check_in_start', $halfDay?->check_in_start ? \Carbon\Carbon::parse($halfDay->check_in_start)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">In End</label><input type="time" name="sessions[half_day][check_in_end]" value="{{ old('sessions.half_day.check_in_end', $halfDay?->check_in_end ? \Carbon\Carbon::parse($halfDay->check_in_end)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">Out Start</label><input type="time" name="sessions[half_day][check_out_start]" value="{{ old('sessions.half_day.check_out_start', $halfDay?->check_out_start ? \Carbon\Carbon::parse($halfDay->check_out_start)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                        <div><label style="font-size: 12px;">Out End</label><input type="time" name="sessions[half_day][check_out_end]" value="{{ old('sessions.half_day.check_out_end', $halfDay?->check_out_end ? \Carbon\Carbon::parse($halfDay->check_out_end)->format('H:i') : '') }}" class="time-input" style="width: 100%; padding: 8px; border-radius: 5px; border: none;"></div>
                    </div>
                </div>
            </div>

            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer;">Update Event</button>
            <a href="{{ route('events.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none;">Cancel</a>
        </form>
    </div>

    <script>
        const dayType = document.getElementById('day_type');
        const availability = document.getElementById('availability');
        const morningBlock = document.getElementById('morning_session');
        const afternoonBlock = document.getElementById('afternoon_session');
        const halfDayBlock = document.getElementById('half_day_session');
        const timeInputs = document.querySelectorAll('.time-input');

        function toggleSessions() {
            if (dayType.value === 'whole_day') {
                morningBlock.style.display = 'block';
                afternoonBlock.style.display = 'block';
                halfDayBlock.style.display = 'none';
            } else {
                morningBlock.style.display = 'none';
                afternoonBlock.style.display = 'none';
                halfDayBlock.style.display = 'block';
            }

            if (availability.value === 'manual') {
                timeInputs.forEach(input => { input.required = false; input.disabled = true; });
            } else {
                timeInputs.forEach(input => { input.disabled = false; });
            }
        }

        dayType.addEventListener('change', toggleSessions);
        availability.addEventListener('change', toggleSessions);
        toggleSessions();
    </script>
@endsection