<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Owner') — Vidya</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #08080a; color: #f5f1e8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 220px; min-height: 100vh; background: #0d0d12; border-right: 1px solid rgba(245,241,232,0.06); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100; }
        .sidebar-logo { padding: 22px 20px 18px; border-bottom: 1px solid rgba(245,241,232,0.06); }
        .sidebar-logo .wordmark { font-size: 17px; font-weight: 800; color: #f5f1e8; letter-spacing: -0.34px; }
        .sidebar-logo .role-badge { font-size: 9px; font-weight: 700; color: #a392c8; text-transform: uppercase; letter-spacing: 1.2px; margin-top: 2px; }
        .sidebar-nav { flex: 1; padding: 14px 0; overflow-y: auto; }
        .nav-section { margin-bottom: 6px; }
        .nav-section-title { font-size: 9px; font-weight: 700; color: #3d3a35; text-transform: uppercase; letter-spacing: 1.4px; padding: 10px 20px 4px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 8px 20px; font-size: 13px; font-weight: 500; color: #6a665f; text-decoration: none; border-left: 2px solid transparent; transition: all 0.15s; }
        .nav-item:hover { color: #d4cfc8; background: rgba(245,241,232,0.03); }
        .nav-item.active { color: #a392c8; background: rgba(163,146,200,0.08); border-left-color: #a392c8; font-weight: 600; }
        .nav-item svg { flex-shrink: 0; opacity: 0.7; }
        .nav-item.active svg { opacity: 1; }

        /* ── Topbar ── */
        .topbar { position: fixed; top: 0; left: 220px; right: 0; height: 54px; background: #0d0d12; border-bottom: 1px solid rgba(245,241,232,0.06); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; z-index: 99; }
        .breadcrumb { font-size: 12px; color: #6a665f; }
        .breadcrumb span { color: #f5f1e8; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .bell-btn { width: 32px; height: 32px; border-radius: 6px; background: transparent; border: 1px solid rgba(245,241,232,0.08); display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6a665f; text-decoration: none; }
        .bell-btn:hover { color: #d4cfc8; background: rgba(245,241,232,0.04); }
        .user-chip { display: flex; align-items: center; gap: 8px; background: rgba(163,146,200,0.1); border: 1px solid rgba(163,146,200,0.2); border-radius: 6px; padding: 5px 10px 5px 5px; }
        .avatar { width: 24px; height: 24px; border-radius: 5px; background: #a392c8; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; color: #fff; letter-spacing: 0.5px; }
        .user-chip-name { font-size: 11px; font-weight: 600; color: #d4cfc8; }
        .user-chip-role { font-size: 9px; color: #a392c8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }

        /* ── Main ── */
        .main { margin-left: 220px; padding-top: 54px; min-height: 100vh; }
        .main-content { padding: 32px 28px; }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="wordmark">Vidya</div>
        <div class="role-badge">Owner</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Business</div>
            <a href="{{ route('owner.course-performance') }}" class="nav-item {{ request()->routeIs('owner.course-performance') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                Course Performance
            </a>
            <a href="{{ route('owner.subject-roi') }}" class="nav-item {{ request()->routeIs('owner.subject-roi*') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/></svg>
                Subject ROI
            </a>
            <a href="{{ route('owner.financial') }}" class="nav-item {{ request()->routeIs('owner.financial') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Financial Summary
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">People</div>
            <a href="{{ route('owner.teachers') }}" class="nav-item {{ request()->routeIs('owner.teachers*') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Teacher Performance
            </a>
            <a href="{{ route('owner.staff-decisions') }}" class="nav-item {{ request()->routeIs('owner.staff-decisions') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Staff Decisions
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Strategic</div>
            <a href="{{ route('owner.strategic-alerts') }}" class="nav-item {{ request()->routeIs('owner.strategic-alerts') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Strategic Alerts
            </a>
            <a href="{{ route('owner.at-risk-students') }}" class="nav-item {{ request()->routeIs('owner.at-risk-students') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                At-Risk Students
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <a href="{{ route('owner.notifications') }}" class="nav-item {{ request()->routeIs('owner.notifications') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Notifications
            </a>
            <a href="{{ route('owner.help') }}" class="nav-item {{ request()->routeIs('owner.help') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Help
            </a>
        </div>
    </nav>
</aside>

<div class="main">
    <header class="topbar">
        <div class="breadcrumb">Owner / <span>@yield('breadcrumb', 'Dashboard')</span></div>
        <div class="topbar-right">
            <a href="{{ route('owner.notifications') }}" class="bell-btn" title="Notifications">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            </a>
            <div class="user-chip">
                <div class="avatar">SA</div>
                <div>
                    <div class="user-chip-name">Sanjay Agarwal</div>
                    <div class="user-chip-role">Owner</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:none;border:none;color:#4a4740;font-size:11px;cursor:pointer;padding:4px 6px;border-radius:4px;" onmouseover="this.style.color='#f5f1e8'" onmouseout="this.style.color='#4a4740'">Sign out</button>
            </form>
        </div>
    </header>
    <main class="main-content">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
