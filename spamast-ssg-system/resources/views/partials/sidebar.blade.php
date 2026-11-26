<div class="sidebar">
    <div class="sidebar-brand">
        <h2>SPAMAST SSG</h2>
        <span>EVENT MANAGEMENT</span>
    </div>

    <div class="sidebar-nav">
        <a href="{{ route('landing') }}">Home</a>

        {{-- Dashboard: dashboardRoute() returns admin.dashboard, officer.dashboard, adviser.dashboard or student.dashboard --}}
        <a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a>
        
        {{-- ADMIN MENU --}}
        @if(auth()->user()->hasRole('Admin'))
            <div class="section-title">Admin Menu</div>
            <a href="{{ route('admin.users.index') }}">Manage Users</a>
            <a href="{{ route('admin.institutes.index') }}">Institutes</a>
            <a href="{{ route('admin.programs.index') }}">Programs</a>
        @endif

        {{-- OFFICER MENU --}}
        @can('manage-ssg')
            <div class="section-title">Officer Menu</div>
            <a href="{{ route('officer.students.index') }}">Manage Students</a>
            <a href="{{ route('events.index') }}">Manage Events</a>
        @endcan

        {{-- REPORTS & VIEWING (Officer & Adviser) --}}
        @can('view-reports')
            <div class="section-title">Reports & Records</div>
            {{-- Officers already have Manage Events above, so only Advisers need this Events link --}}
            @cannot('manage-ssg')
                <a href="{{ route('events.index') }}">Events</a>
            @endcannot
            <a href="{{ route('reports.index') }}">Event Reports</a>
        @endcan

        {{-- STUDENT MENU --}}
        @can('use-attendance')
            <div class="section-title">Student Menu</div>
            <a href="{{ route('events.index') }}">Upcoming Events</a>
        @endcan

        {{-- ANNOUNCEMENTS (Officer, Adviser and Student; same roles as the shared routes) --}}
        @if(auth()->user()->hasRole('Officer', 'Adviser', 'Student'))
            <div class="section-title">Announcements</div>
            <a href="{{ route('announcements.index') }}">Announcements</a>
        @endif
    </div>
</div>