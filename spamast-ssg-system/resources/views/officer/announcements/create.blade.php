@extends('layouts.app')

@section('title', 'New Announcement')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">New Announcement</h1>

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

        {{-- enctype is required so the file is sent to the server --}}
        <form method="POST" action="{{ route('officer.announcements.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Body</label>
                <textarea name="body" style="width: 100%; padding: 10px; border-radius: 8px; border: none; height: 160px;" required>{{ old('body') }}</textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 5px;">Attachment (Optional)</label>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp" style="width: 100%; padding: 10px; border-radius: 8px; border: none; background: white; color: black;">
                <small style="color: #94a3b8;">PDF, JPG, PNG or WEBP. Maximum 5 MB.</small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Post Announcement</button>
                <a href="{{ route('announcements.index') }}" style="padding: 12px 20px; color: #d8b4fe; text-decoration: none;">Cancel</a>
            </div>
        </form>
    </div>
@endsection