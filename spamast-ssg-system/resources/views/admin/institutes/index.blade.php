@extends('layouts.app')

@section('title', 'Manage Institutes')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #c084fc;">Institutes</h1>
        <a href="{{ route('admin.institutes.create') }}" style="background: #7c3aed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Add Institute</a>
    </div>

    @if(session('error'))
        <div style="background: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">{{ session('error') }}</div>
    @endif

    <div class="card">
        <form method="GET" action="{{ route('admin.institutes.index') }}" style="display: flex; gap: 15px; margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by name or code..." value="{{ $search }}" style="padding: 10px; border-radius: 8px; border: none; flex: 1;">
            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Search</button>
            @if($search)
                <a href="{{ route('admin.institutes.index') }}" style="padding: 10px 20px; color: #d8b4fe; text-decoration: none;">Clear</a>
            @endif
        </form>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Code</th>
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($institutes as $institute)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $institute->code }}</td>
                        <td style="padding: 12px;">{{ $institute->name }}</td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('admin.institutes.edit', $institute->id) }}" style="color: #fbbf24; text-decoration: none; margin-right: 10px;">Edit</a>
                            <form method="POST" action="{{ route('admin.institutes.destroy', $institute->id) }}" style="display: inline;" onsubmit="return confirm('Delete this institute?');">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: transparent; border: none; color: #ef4444; cursor: pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 20px; text-align: center;">No institutes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $institutes->links() }}
        </div>
    </div>
@endsection