@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #c084fc;">Announcements</h1>

        {{-- Only Officers can post --}}
        @can('manage-ssg')
            <a href="{{ route('officer.announcements.create') }}" style="background: #7c3aed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">+ New Announcement</a>
        @endcan
    </div>

    @forelse($announcements as $announcement)
        <div class="card">
            <h2>
                <a href="{{ route('announcements.show', $announcement) }}" style="color: #e9d5ff; text-decoration: none;">{{ $announcement->title }}</a>
            </h2>
            <p style="color: #94a3b8; font-size: 13px; margin-bottom: 12px;">
                Posted by {{ $announcement->user->first_name ? $announcement->user->first_name . ' ' . $announcement->user->last_name : $announcement->user->name }}
                on {{ $announcement->created_at->format('F d, Y h:i A') }}
            </p>

            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('announcements.show', $announcement) }}" style="color: #d8b4fe; font-weight: bold; text-decoration: none;">Read more</a>

                {{-- Attachment link (file is on the public disk) --}}
                @if($announcement->attachment)
                    <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank" style="color: #d8b4fe; text-decoration: none;">View attachment</a>
                @endif

                {{-- Officer controls --}}
                @can('manage-ssg')
                    <a href="{{ route('officer.announcements.edit', $announcement) }}" style="background: #fbbf24; color: black; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px;">Edit</a>
                    <form method="POST" action="{{ route('officer.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px;">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
    @empty
        <div class="card">
            <p>No announcements yet.</p>
        </div>
    @endforelse

    <div style="margin-top: 20px;">
        {{ $announcements->links() }}
    </div>
@endsection