<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Blog Services') | Admin Panel</title>
    <link rel="icon" type="image/png" href="/anvis-favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- No icon library needed - using inline SVGs -->
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Light Theme Variables */
            --bg:          #f4f7fe;
            --surface:     #ffffff;
            --surface2:    #f8fafc;
            --border:      #e2e8f0;
            --text-main:   #1e293b;
            --text-muted:  #64748b;
            
            /* Sidebar Variables (Dark) */
            --sb-bg:       #0f172a;
            --sb-surface:  #1e293b;
            --sb-border:   #334155;
            --sb-text:     #94a3b8;
            --sb-text-h:   #f8fafc;

            /* Accents & States */
            --accent:      #4f46e5;
            --accent-h:    #4338ca;
            --accent-light:#e0e7ff;
            --success:     #10b981;
            --success-bg:  #d1fae5;
            --danger:      #ef4444;
            --danger-bg:   #fee2e2;
            --warning:     #f59e0b;
            --warning-bg:  #fef3c7;
            
            --sidebar-w:   260px;
            --shadow-sm:   0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md:   0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg:   0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius-md:   12px;
            --radius-lg:   16px;
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            background: var(--bg); 
            color: var(--text-main); 
            min-height: 100vh; 
            display: flex; 
            -webkit-font-smoothing: antialiased;
        }

        /* ── Sidebar (Premium Dark) ── */
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh; background: var(--sb-bg);
            display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100;
            box-shadow: 4px 0 24px rgba(0,0,0,0.05);
        }
        .sidebar-logo {
            padding: 24px; display: flex; align-items: center; gap: 14px;
            border-bottom: 1px solid var(--sb-border);
        }
        .sidebar-logo .icon {
            width: 38px; height: 38px; background: linear-gradient(135deg, var(--accent), #818cf8);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .sidebar-logo span { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.5px; }
        .sidebar-logo small { display: block; font-size: 11px; color: var(--sb-text); font-weight: 500; margin-top: 2px; }

        .sidebar-nav { padding: 24px 16px; flex: 1; overflow-y: auto; }
        .nav-label { 
            font-size: 11px; font-weight: 600; text-transform: uppercase; 
            letter-spacing: 1.2px; color: var(--sb-text); padding: 0 12px 10px; margin-top: 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px;
            border-radius: 10px; text-decoration: none; color: var(--sb-text);
            font-size: 14px; font-weight: 500; transition: all .2s ease; margin-bottom: 4px;
        }
        .nav-item i { width: 20px; text-align: center; font-size: 15px; opacity: 0.8; transition: transform .2s; }
        .nav-item:hover { background: var(--sb-surface); color: var(--sb-text-h); }
        .nav-item:hover i { transform: translateX(3px); opacity: 1; }
        .nav-item.active { background: var(--accent); color: #fff; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25); }
        .nav-item.active i { opacity: 1; }

        /* ── Main Layout ── */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Topbar (Clean & Airy) ── */
        .topbar {
            height: 70px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 18px; font-weight: 600; color: var(--text-main); letter-spacing: -0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .date-badge { 
            background: var(--surface2); padding: 6px 12px; border-radius: 20px; 
            font-size: 12px; font-weight: 500; color: var(--text-muted); border: 1px solid var(--border);
        }
        .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #818cf8);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff; cursor: pointer;
            box-shadow: var(--shadow-sm); border: 2px solid #fff;
        }

        .content { padding: 32px; flex: 1; max-width: 1400px; margin: 0 auto; width: 100%; }

        /* ── Page Header ── */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .page-header h1 { font-size: 26px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; }
        .page-header p  { font-size: 14px; color: var(--text-muted); margin-top: 6px; line-height: 1.5; }

        /* ── Breadcrumbs ── */
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--text-muted); margin-bottom: 12px; }
        .breadcrumb a { color: var(--accent); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .sep { font-size: 10px; color: #cbd5e1; }

        /* ── Cards (Soft Glass/Shadow) ── */
        .card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);
            overflow: hidden; margin-bottom: 24px; transition: box-shadow .3s ease;
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--surface);
        }
        .card-header h2 { font-size: 16px; font-weight: 600; display: flex; align-items: center; color: var(--text-main); }
        .card-body { padding: 24px; }

        /* ── Stats ── */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
            padding: 24px; display: flex; align-items: center; gap: 20px;
            box-shadow: var(--shadow-sm); transition: transform .3s ease, box-shadow .3s ease;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--accent-light); }
        .stat-icon {
            width: 54px; height: 54px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
        }
        .stat-icon.purple { background: var(--accent-light); color: var(--accent); }
        .stat-icon.green  { background: var(--success-bg); color: var(--success); }
        .stat-icon.amber  { background: var(--warning-bg); color: var(--warning); }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-main); line-height: 1.1; }
        .stat-label { font-size: 13px; font-weight: 500; color: var(--text-muted); margin-top: 6px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; 
            cursor: pointer; border: 1px solid transparent; text-decoration: none;
            transition: all .2s ease; font-family: inherit; line-height: 1.5;
        }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2); }
        .btn-primary:hover { background: var(--accent-h); transform: translateY(-1px); box-shadow: 0 6px 12px rgba(79, 70, 229, 0.3); }
        .btn-success { background: var(--success); color: #fff; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); }
        .btn-success:hover { background: #059669; }
        .btn-danger  { background: var(--danger); color: #fff; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2); }
        .btn-danger:hover  { background: #dc2626; }
        .btn-ghost { background: var(--surface2); color: var(--text-main); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--border); }
        .btn-sm { padding: 7px 14px; font-size: 13px; border-radius: 6px; }

        /* ── Tables ── */
        .table-wrap { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        thead th { 
            padding: 14px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; 
            letter-spacing: 0.8px; color: var(--text-muted); text-align: left;
            background: var(--surface2); border-bottom: 1px solid var(--border);
        }
        tbody tr { transition: background .2s ease; }
        tbody tr:hover { background: var(--surface2); }
        tbody td { 
            padding: 16px 20px; font-size: 14px; border-bottom: 1px solid var(--border);
            color: var(--text-main); vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-danger  { background: var(--danger-bg);  color: var(--danger); }
        .badge-purple  { background: var(--accent-light); color: var(--accent); }

        /* ── Forms ── */
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
        .form-label span { color: var(--danger); margin-left: 2px; }
        .form-control {
            width: 100%; padding: 12px 16px; background: var(--surface);
            border: 1px solid var(--border); border-radius: 8px; color: var(--text-main);
            font-size: 14px; font-family: inherit; transition: all .2s ease;
            box-shadow: var(--shadow-sm); outline: none;
        }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); }
        .form-control::placeholder { color: #94a3b8; }
        textarea.form-control { resize: vertical; min-height: 100px; line-height: 1.5; }
        select.form-control { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px; padding-right: 40px; }

        /* Toggle */
        .toggle-wrap { display: flex; align-items: center; gap: 12px; }
        .toggle { position: relative; width: 48px; height: 26px; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0; background: #cbd5e1;
            border-radius: 34px; cursor: pointer; transition: .3s ease;
        }
        .toggle-slider::before {
            content: ''; position: absolute; height: 20px; width: 20px;
            left: 3px; bottom: 3px; background: #fff; border-radius: 50%; 
            transition: .3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .toggle input:checked + .toggle-slider { background: var(--success); }
        .toggle input:checked + .toggle-slider::before { transform: translateX(22px); }
        .toggle-label { font-size: 14px; font-weight: 500; color: var(--text-main); }

        /* ── Alerts ── */
        .alert { padding: 16px 20px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; box-shadow: var(--shadow-sm); }
        .alert-success { background: var(--success-bg); border: 1px solid #a7f3d0; color: #065f46; }
        .alert-danger  { background: var(--danger-bg);  border: 1px solid #fecaca; color: #991b1b; }
        .alert i { font-size: 18px; margin-top: 2px; }

        /* ── Image preview ── */
        .img-preview { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        .no-img { width: 80px; height: 60px; background: var(--surface2); border-radius: 8px; border: 1px dashed var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 20px; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 54px; color: #cbd5e1; margin-bottom: 20px; }
        .empty-state h3 { font-size: 18px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
        .empty-state p { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; max-width: 400px; margin-inline: auto; }

        /* ── Utilities ── */
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 13px; }
        .mt-2 { margin-top: 8px; }
        .d-flex { display: flex; }
        .gap-2 { gap: 12px; }
        .ms-auto { margin-left: auto; }
        .align-center { align-items: center; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 960px) { .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .stats { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="icon" style="background:transparent;box-shadow:none;padding:2px">
                <img src="/anvis-favicon.png" alt="Logo" style="width:34px;height:34px;object-fit:contain;border-radius:6px">
            </div>
            <div>
                <span>BlogServices</span>
                <small>Admin Panel</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            <div class="nav-label" style="margin-top:12px">Manage</div>
            <a href="{{ route('admin.companies.index') }}" class="nav-item {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                Companies
            </a>
            <a href="{{ route('admin.blogs.index') }}" class="nav-item {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                Blogs
            </a>
        </nav>

        {{-- Sidebar Footer: User info + Logout --}}
        <div style="padding:16px;border-top:1px solid var(--sb-border)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;padding:10px 12px;border-radius:10px;background:rgba(255,255,255,0.04)">
                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div style="overflow:hidden">
                    <div style="font-size:13px;font-weight:600;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div style="font-size:11px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;border:none;background:rgba(239,68,68,0.08);color:#f87171;font-size:13px;font-weight:500;font-family:inherit;cursor:pointer;transition:all .2s ease" onmouseover="this.style.background='rgba(239,68,68,0.15)'" onmouseout="this.style.background='rgba(239,68,68,0.08)'">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="main">
        <header class="topbar">
            <div class="topbar-title">@yield('topbar-title', 'Dashboard')</div>
            <div class="topbar-right">
                <span style="font-size:12px;color:var(--text-muted);background:var(--surface2);padding:6px 14px;border-radius:20px;border:1px solid var(--border)">{{ now()->format('D, d M Y') }}</span>
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="text-align:right">
                        <div style="font-size:13px;font-weight:600;color:var(--text-main)">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">Administrator</div>
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
</body>
</html>
