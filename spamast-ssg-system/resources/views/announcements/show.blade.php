@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #c084fc;">{{ $announcement->title }}</h1>
        <a href="{{ route('announcements.index') }}" style="color: #d8b4fe; text-decoration: none; font-weight: bold;">&larr; Back to Announcements</a>
    </div>

    <div class="card" style="max-width: 800px;">
        <p style="color: #94a3b8; font-size: 13px; margin-bottom: 15px;">
            Posted by {{ $announcement->user->full_name }}
            on {{ $announcement->created_at->format('F d, Y h:i A') }}
        </p>

        {{-- e() escapes HTML for safety, nl2br keeps the line breaks --}}
        <div style="line-height: 1.6; margin-bottom: 20px;">{!! nl2br(e($announcement->body)) !!}</div>

        @if($announcement->attachment)
            @php
                $extension = strtolower(pathinfo($announcement->attachment, PATHINFO_EXTENSION));
            @endphp

            {{-- Show images directly, link to PDFs --}}
            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                <img src="{{ asset('storage/' . $announcement->attachment) }}" alt="Attachment" style="max-width: 100%; border-radius: 10px; margin-bottom: 15px;">
            @endif

            <p>
                <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank" style="color: #d8b4fe; font-weight: bold;">Open attachment</a>
            </p>
        @endif

        @can('manage-ssg')
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <a href="{{ route('officer.announcements.edit', $announcement) }}" style="background: #fbbf24; color: black; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold;">Edit</a>
                <form method="POST" action="{{ route('officer.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: bold;">Delete</button>
                </form>
            </div>
        @endcan
    </div>
@endsection