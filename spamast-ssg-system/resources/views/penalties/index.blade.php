@extends('layouts.app')

@section('title', 'Penalties')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 28px; color: #c084fc;">Penalties: {{ $event->title }}</h1>
            <p style="color: #94a3b8;">{{ $event->event_date->format('F d, Y') }}</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 14px; color: #94a3b8;">Total Fines</div>
            <div style="font-size: 24px; font-weight: bold; color: #4ade80;">₱{{ number_format($totalAmount, 2) }}</div>
        </div>
    </div>

    @if(session('error'))
        <div style="background: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">{{ session('success') }}</div>
    @endif

    <div class="card">
        <form method="GET" action="{{ route('penalties.index', $event) }}" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
            
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

            <select name="status" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Statuses</option>
                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="served" {{ request('status') == 'served' ? 'selected' : '' }}>Served</option>
            </select>
            
            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Filter</button>
            <a href="{{ route('penalties.index', $event) }}" style="padding: 10px 20px; color: #d8b4fe; text-decoration: none;">Clear</a>
        </form>

        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Student No</th>
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px;">Program</th>
                    <th style="padding: 12px;">Missed Scans</th>
                    <th style="padding: 12px;">Amount</th>
                    <th style="padding: 12px;">Sanction</th>
                    <th style="padding: 12px;">Status</th>
                    @can('manage-ssg')
                        <th style="padding: 12px; text-align: right;">Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($penalties as $penalty)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $penalty->student->student_no }}</td>
                        <td style="padding: 12px;">{{ $penalty->student->user->first_name }} {{ $penalty->student->user->last_name }}</td>
                        <td style="padding: 12px;">{{ $penalty->student->program->code }}</td>
                        <td style="padding: 12px; font-weight: bold; color: #ef4444;">{{ $penalty->missed_scans }}</td>
                        <td style="padding: 12px;">₱{{ number_format($penalty->amount, 2) }}</td>
                        <td style="padding: 12px;">{{ $penalty->sanction ?? '--' }}</td>
                        <td style="padding: 12px;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                background: {{ $penalty->status === 'unpaid' ? '#ef4444' : ($penalty->status === 'paid' ? '#4ade80' : '#fbbf24') }};
                                color: {{ $penalty->status === 'unpaid' ? 'white' : 'black' }};">
                                {{ ucfirst($penalty->status) }}
                            </span>
                        </td>
                        @can('manage-ssg')
                            <td style="padding: 12px; text-align: right; display: flex; gap: 5px; justify-content: flex-end;">
                                <button type="button" onclick="openEditModal({{ $penalty->id }}, '{{ $penalty->amount }}', '{{ $penalty->sanction }}', '{{ $penalty->status }}')" style="background: #fbbf24; color: black; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px;">Edit</button>
                                <form method="POST" action="{{ route('officer.penalties.destroy', $penalty) }}" onsubmit="return confirm('Delete this penalty?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px;">Delete</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 20px; text-align: center;">No penalties found for this event.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $penalties->links() }}
        </div>
    </div>

    @can('manage-ssg')
        <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; z-index: 1000;">
            <div class="card" style="width: 400px; background: #1e1b4b;">
                <h2>Edit Penalty</h2>
                <form id="editForm" method="POST" style="margin-top: 15px;">
                    @csrf 
                    @method('PUT')
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Amount (₱)</label>
                        <input type="number" name="amount" id="edit_amount" step="0.01" min="0" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Sanction</label>
                        <input type="text" name="sanction" id="edit_sanction" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Status</label>
                        <select name="status" id="edit_status" style="width: 100%; padding: 10px; border-radius: 8px; border: none;">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="served">Served</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" style="background: #4ade80; color: black; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;">Save</button>
                        <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditModal(id, amount, sanction, status) {
                document.getElementById('editForm').action = `/officer/penalties/${id}`;
                document.getElementById('edit_amount').value = amount;
                document.getElementById('edit_sanction').value = sanction;
                document.getElementById('edit_status').value = status;
                document.getElementById('editModal').style.display = 'flex';
            }
        </script>
    @endcan

    <script>
        document.getElementById('institute_filter').addEventListener('change', function() {
            const instituteId = this.value;
            const programSelect = document.getElementById('program_filter');
            const options = programSelect.querySelectorAll('option');

            programSelect.value = '';

            options.forEach(option => {
                if (!option.value) return; 
                if (!instituteId || option.getAttribute('data-institute') === instituteId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        if (document.getElementById('institute_filter').value) {
            document.getElementById('institute_filter').dispatchEvent(new Event('change'));
        }
    </script>
@endsection