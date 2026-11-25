<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SPAMAST SSG</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #2e1065, #1e1b4b);
            min-height: 100vh;
            color: white;
            display: flex;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: rgba(0, 0, 0, 0.2);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        .sidebar-brand h2 { font-size: 20px; color: #c084fc; }
        .sidebar-brand span { font-size: 11px; color: #94a3b8; letter-spacing: 2px; }
        
        .sidebar-nav a {
            display: block;
            padding: 12px 20px;
            color: #d8b4fe;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .sidebar-nav a:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .sidebar-nav .section-title {
            padding: 15px 20px 5px;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }

        /* Main Content */
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        
        /* Top Navbar */
        .top-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .avatar {
            width: 40px; height: 40px;
            background: #7c3aed;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 16px;
        }
        .user-details h4 { font-size: 14px; margin-bottom: 2px; }
        .user-details span { font-size: 12px; color: #d8b4fe; }
        
        .nav-actions form { display: inline; }
        .btn-logout {
            background: #ef4444; color: white; border: none;
            padding: 8px 15px; border-radius: 8px;
            font-weight: bold; cursor: pointer; font-size: 13px;
            transition: background 0.2s;
        }
        .btn-logout:hover { background: #dc2626; }

        /* Content Area */
        .content { padding: 40px; flex: 1; }
        
        /* Flash Messages (dismissible) */
        .alert {
            display: flex;                    /* message on the left, X button on the right */
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-success { background: #dcfce7; color: #166534; }  /* same green as before */
        .alert-error   { background: #ef4444; color: white; }    /* same red used on other pages */
        .alert-close {
            background: none; border: none;
            color: inherit;                   /* X takes the alert's text color */
            font-size: 20px; line-height: 1;
            cursor: pointer; opacity: 0.7;
        }
        .alert-close:hover { opacity: 1; }

        /* Cards & Stats */
        .card {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }
        .card h2 { margin-bottom: 10px; color: #e9d5ff; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .stat-box h3 { font-size: 14px; color: #94a3b8; margin-bottom: 10px; }
        .stat-box .number { font-size: 28px; font-weight: bold; color: #c084fc; }

        /* Pagination Styling */
        .pagination {
            display: flex;
            gap: 5px;
            list-style: none;
            margin-top: 20px;
            padding: 0;
        }
        .page-item .page-link {
            display: block;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            color: #d8b4fe;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
        }
        .page-item.active .page-link {
            background: #7c3aed;
            color: white;
            border-color: #7c3aed;
        }
        .page-item .page-link:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .page-item.disabled .page-link {
            color: #64748b;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="user-info">
                <div class="avatar">
                    {{ substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->last_name ?? '', 0, 1) }}
                </div>
                <div class="user-details">
                    <h4>
                        {{ auth()->user()->first_name ? auth()->user()->first_name . ' ' . auth()->user()->last_name : auth()->user()->name }}
                    </h4>
                    <span>{{ auth()->user()->role?->name }} Dashboard</span>
                </div>
            </div>
            
            <div class="nav-actions">
                <a href="{{ route('profile.edit') }}" style="color: #d8b4fe; text-decoration: none; margin-right: 20px; font-weight: bold;">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </header>

        <!-- Content -->
        <main class="content">
            {{-- Flash messages: session keys "success" and "error" --}}
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <span>{{ session('success') }}</span>
                    {{-- Removes this alert from the page when clicked --}}
                    <button type="button" class="alert-close" aria-label="Close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="alert-close" aria-label="Close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>