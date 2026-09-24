<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Blog Services') | Admin Panel</title>
    <link rel="icon" type="image/png" href="/anvis-favicon.png">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Modern Theme Variables */
            --bg:          #f4f6f9;
            --surface:     #ffffff;
            --surface2:    #f8fafc;
            --border:      #e2e8f0;
            --text-main:   #1e293b;
            --text-muted:  #64748b;
            
            /* Sidebar Variables */
            --sb-bg:       #ffffff;
            --sb-surface:  #f1f5f9;
            --sb-text:     #64748b;
            --sb-text-h:   #0f172a;

            /* Accents & States */
            --accent:      #2563eb;
            --accent-h:    #1d4ed8;
            --accent-light:#eff6ff;
            --success:     #10b981;
            --success-bg:  #ecfdf5;
            --danger:      #ef4444;
            --danger-bg:   #fef2f2;
            --warning:     #f59e0b;
            --warning-bg:  #fffbeb;
            
            --sidebar-w:   250px;
            --shadow-sm:   0 2px 4px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-md:   0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --shadow-lg:   0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            
            --radius-sm:   10px;
            --radius-md:   14px;
            --radius-lg:   20px;
            --radius-xl:   24px;
        }

        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background: var(--bg); 
            color: var(--text-main); 
            min-height: 100vh; 
            display: flex; 
        }

        /* ── Sidebar (Floating & Rounded) ── */
        .sidebar {
            width: var(--sidebar-w); 
            min-height: 100vh; 
            background: var(--sb-bg);
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 100;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.03);
        }
        .sidebar-logo {
            padding: 20px 18px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
        }
        .sidebar-logo .icon {
            width: 36px; 
            height: 36px; 
            background: var(--accent-light);
            border-radius: var(--radius-sm); 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .sidebar-logo span { font-size: 16px; font-weight: 700; color: var(--text-main); letter-spacing: -0.3px; }
        .sidebar-logo small { display: block; font-size: 11px; color: var(--text-muted); font-weight: 500; }

        .sidebar-nav { padding: 10px 14px; flex: 1; overflow-y: auto; }
        .nav-label { 
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--sb-text); padding: 0 10px 8px; margin-top: 18px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 11px 14px;
            border-radius: var(--radius-md); text-decoration: none; color: var(--sb-text);
            font-size: 14px; font-weight: 500; margin-bottom: 4px;
            transition: all 0.2s ease;
        }
        .nav-item svg { transition: transform 0.2s ease; }
        .nav-item:hover { background: var(--sb-surface); color: var(--sb-text-h); }
        .nav-item:hover svg { transform: translateX(2px); }
        .nav-item.active { background: var(--accent); color: #fff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }

        /* ── Main Layout ── */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Topbar (Border-free & Floating Style) ── */
        .topbar {
            height: 70px; 
            background: rgba(244, 246, 249, 0.9);
            backdrop-filter: blur(10px);
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            padding: 0 32px; 
            position: sticky; 
            top: 0; 
            z-index: 50;
        }
        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .topbar-title { font-size: 20px; font-weight: 700; color: var(--text-main); letter-spacing: -0.3px; }
        .menu-toggle {
            display: none; background: transparent; border: none; font-size: 24px; color: var(--text-main); cursor: pointer;
        }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .date-badge { 
            font-size: 13px; font-weight: 500; color: var(--text-muted); 
            background: var(--surface); padding: 8px 16px; border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }
        .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--accent); display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600; color: #fff; cursor: pointer;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
            transition: transform 0.2s ease;
        }
        .avatar:hover { transform: scale(1.05); }

        .content { padding: 10px 32px 32px; flex: 1; max-width: 1280px; margin: 0 auto; width: 100%; }

        /* ── Page Header ── */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.4px; }
        .page-header p  { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

        /* ── Breadcrumbs ── */
        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); margin-bottom: 12px; font-weight: 500; }
        .breadcrumb a { color: var(--accent); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .sep { font-size: 12px; color: #cbd5e1; }

        /* ── Cards (Seamless & Soft Soft-Shadows) ── */
        .card {
            background: var(--surface); 
            border: none;
            border-radius: var(--radius-xl); 
            box-shadow: var(--shadow-md);
            margin-bottom: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover { 
            box-shadow: var(--shadow-lg); 
            transform: translateY(-2px);
        }
        .card-header {
            padding: 22px 28px;
            background: transparent;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-header h2 { font-size: 18px; font-weight: 700; margin: 0; color: var(--text-main); }
        .card-body { padding: 0 28px 28px; }

        /* ── Stats ── */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface); border: none; border-radius: var(--radius-lg);
            padding: 22px; display: flex; align-items: center; gap: 16px;
            box-shadow: var(--shadow-sm); transition: transform 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center; font-size: 20px;
            background: var(--accent-light); color: var(--accent); border: none;
        }
        .stat-value { font-size: 26px; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; }
        .stat-label { font-size: 13px; font-weight: 500; color: var(--text-muted); margin-top: 2px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 20px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600;
            cursor: pointer; border: none; text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
        .btn-primary:hover { background: var(--accent-h); transform: translateY(-1px); }
        .btn-success { background: var(--success); color: #fff; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
        .btn-success:hover { background: #059669; transform: translateY(-1px); }
        .btn-danger  { background: var(--danger); color: #fff; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }
        .btn-danger:hover  { background: #dc2626; transform: translateY(-1px); }
        .btn-ghost { background: var(--surface2); color: var(--text-main); }
        .btn-ghost:hover { background: #e2e8f0; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: var(--radius-sm); }

        /* ── Tables ── */
        .table-wrap { overflow-x: auto; width: 100%; border-radius: var(--radius-md); }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        thead th { 
            padding: 14px 18px; font-size: 12px; font-weight: 700; 
            color: var(--text-muted); text-align: left; text-transform: uppercase; letter-spacing: 0.5px;
            background: var(--surface2); border: none;
        }
        thead th:first-child { border-top-left-radius: var(--radius-md); border-bottom-left-radius: var(--radius-md); }
        thead th:last-child { border-top-right-radius: var(--radius-md); border-bottom-right-radius: var(--radius-md); }
        tbody tr:hover { background: var(--surface2); }
        tbody td { 
            padding: 16px 18px; font-size: 14px; border: none;
            color: var(--text-main); vertical-align: middle;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-danger  { background: var(--danger-bg);  color: var(--danger); }
        .badge-purple  { background: var(--accent-light); color: var(--accent); }

        /* ── Forms ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .form-label span { color: var(--danger); margin-left: 2px; }
        .form-control {
            width: 100%; padding: 12px 16px; background: var(--surface2);
            border: 2px solid transparent; border-radius: var(--radius-md); color: var(--text-main);
            font-size: 14px; transition: all 0.2s ease;
        }
        .form-control:focus { 
            background: #fff;
            border-color: var(--accent);
            outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); 
        }
        textarea.form-control { resize: vertical; min-height: 100px; }
        select.form-control { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px; padding-right: 40px; }

        /* Toggle */
        .toggle-wrap { display: flex; align-items: center; gap: 12px; }
        .toggle { position: relative; width: 50px; height: 28px; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0; background: #cbd5e1;
            border-radius: 34px; cursor: pointer; transition: .3s ease;
        }
        .toggle-slider::before {
            content: ''; position: absolute; height: 22px; width: 22px;
            left: 3px; bottom: 3px; background: #fff; border-radius: 50%; 
            transition: .3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .toggle input:checked + .toggle-slider { background: var(--success); }
        .toggle input:checked + .toggle-slider::before { transform: translateX(22px); }
        .toggle-label { font-size: 14px; font-weight: 500; color: var(--text-main); }

        /* ── Alerts ── */
        .alert { padding: 16px 20px; border-radius: var(--radius-lg); font-size: 14px; font-weight: 500; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; border: none; box-shadow: var(--shadow-sm); }
        .alert-success { background: var(--success-bg); color: #047857; }
        .alert-danger  { background: var(--danger-bg);  color: #b91c1c; }

        /* ── Image preview ── */
        .img-preview { width: 80px; height: 60px; object-fit: cover; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); }
        .no-img { width: 80px; height: 60px; background: var(--surface2); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 20px; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 54px; color: #cbd5e1; margin-bottom: 20px; }
        .empty-state h3 { font-size: 18px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
        .empty-state p { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; max-width: 400px; margin-inline: auto; }

        /* ── Sidebar Footer Box ── */
        .sidebar-footer {
            padding: 16px; margin: 12px; background: var(--sb-surface);
            border-radius: var(--radius-lg);
        }

        /* ── Utilities ── */
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 13px; }
        .mt-2 { margin-top: 8px; }
        .d-flex { display: flex; }
        .gap-2 { gap: 12px; }
        .form-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; max-width: 1200px; margin: 0 auto; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 960px) { 
            .form-row { grid-template-columns: 1fr; } 
            .form-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar.show { transform: translateX(0); }
            .main { margin-left: 0; }
            .stats { grid-template-columns: 1fr; }
            .topbar { padding: 0 16px; }
            .topbar-right .date-badge { display: none; }
            .topbar-right .user-info { display: none; }
            .menu-toggle { display: block; }
            .content { padding: 10px 16px 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="icon">
                <img src="/anvis-favicon.png" alt="Logo" style="width:24px;height:24px;object-fit:contain">
            </div>
            <div>
                <span>BlogServices</span>
                <small>Admin Panel</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>

            <div class="nav-label">Manage</div>
            <a href="{{ route('admin.companies.index') }}" class="nav-item {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="3"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                Companies
            </a>
            <a href="{{ route('admin.blogs.index') }}" class="nav-item {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                Blogs
            </a>
        </nav>

        {{-- Sidebar Footer: User info + Logout --}}
        <div class="sidebar-footer">
            <div style="margin-bottom:12px;font-size:13px;">
                <strong style="color:var(--text-main);">{{ auth()->user()->name ?? 'Admin' }}</strong><br>
                <span style="color:var(--text-muted);font-size:11px;">{{ auth()->user()->email ?? '' }}</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger" style="width:100%; font-size: 12px; justify-content: center;">
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <div class="topbar-title">@yield('topbar-title', 'Dashboard')</div>
            </div>
            <div class="topbar-right">
                <span class="date-badge">{{ now()->format('D, d M Y') }}</span>
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="text-align:right" class="user-info">
                        <div style="font-size:13px;font-weight:700;color:var(--text-main)">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Administrator</div>
                    </div>
                    <div class="avatar" title="{{ auth()->user()->name ?? 'Admin' }}">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">&#10003; {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">&#9888; {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>