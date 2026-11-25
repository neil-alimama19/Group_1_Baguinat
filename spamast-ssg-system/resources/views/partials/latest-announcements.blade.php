{{-- Expects $announcements: the 3 newest announcements, passed by DashboardController --}}
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h2 style="margin-bottom: 0;">Latest Announcements</h2>
        <a href="{{ route('announcements.index') }}" style="color: #d8b4fe; text-decoration: none; font-weight: bold;">View all &rarr;</a>
    </div>

    @forelse($announcements as $announcement)
        <div style="padding: 12px 0; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <a href="{{ route('announcements.show', $announcement) }}" style="color: white; font-weight: bold; text-decoration: none;">{{ $announcement->title }}</a>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 3px;">{{ $announcement->created_at->format('F d, Y') }}</div>
        </div>
    @empty
        <p style="color: #94a3b8;">No announcements yet.</p>
    @endforelse
</div>