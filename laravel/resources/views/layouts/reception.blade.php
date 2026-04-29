<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reception') — Vidya</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #08080a; color: #f5f1e8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; min-height: 100vh; background: #0d0d12; border-right: 1px solid rgba(245,241,232,0.06); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100; }
        .sidebar-logo { padding: 22px 20px 18px; border-bottom: 1px solid rgba(245,241,232,0.06); }
        .sidebar-logo .wordmark { font-size: 17px; font-weight: 800; color: #f5f1e8; letter-spacing: -0.34px; }
        .sidebar-logo .role-badge { font-size: 9px; font-weight: 700; color: #c87064; text-transform: uppercase; letter-spacing: 1.2px; margin-top: 2px; }
        .sidebar-nav { flex: 1; padding: 14px 0; overflow-y: auto; }
        .nav-section-title { font-size: 9px; font-weight: 700; color: #3d3a35; text-transform: uppercase; letter-spacing: 1.4px; padding: 10px 20px 4px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 8px 20px; font-size: 13px; font-weight: 500; color: #6a665f; text-decoration: none; border-left: 2px solid transparent; transition: all 0.15s; }
        .nav-item:hover { color: #d4cfc8; background: rgba(245,241,232,0.03); }
        .nav-item.active { color: #c87064; background: rgba(200,112,100,0.08); border-left-color: #c87064; font-weight: 600; }
        .nav-item svg { flex-shrink: 0; opacity: 0.7; }
        .nav-item.active svg { opacity: 1; }

        /* ── Topbar ── */
        .topbar { position: fixed; top: 0; left: 210px; right: 0; height: 54px; background: #0d0d12; border-bottom: 1px solid rgba(245,241,232,0.06); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; z-index: 99; }
        .breadcrumb { font-size: 12px; color: #6a665f; }
        .breadcrumb span { color: #f5f1e8; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .user-chip { display: flex; align-items: center; gap: 8px; background: rgba(200,112,100,0.1); border: 1px solid rgba(200,112,100,0.2); border-radius: 6px; padding: 5px 10px 5px 5px; }
        .avatar { width: 24px; height: 24px; border-radius: 5px; background: #c87064; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; color: #fff; letter-spacing: 0.5px; }
        .user-chip-name { font-size: 11px; font-weight: 600; color: #d4cfc8; }
        .user-chip-role { font-size: 9px; color: #c87064; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }

        /* ── Main ── */
        .main { margin-left: 210px; padding-top: 54px; min-height: 100vh; }
        .main-content { padding: 32px 28px; }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="wordmark">Vidya</div>
        <div class="role-badge">Reception</div>
    </div>
    <nav class="sidebar-nav">
        <div>
            <a href="{{ route('reception.dashboard') }}" class="nav-item {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
        </div>

        <div>
            <div class="nav-section-title">Lookup</div>
            <a href="{{ route('reception.students') }}" class="nav-item {{ request()->routeIs('reception.students*') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Search Students
            </a>
            <a href="{{ route('reception.tests') }}" class="nav-item {{ request()->routeIs('reception.tests*') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                All Tests
            </a>
        </div>

        <div>
            <div class="nav-section-title">Results</div>
            <a href="{{ route('reception.walk-ins') }}" class="nav-item {{ request()->routeIs('reception.walk-ins') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Recent Walk-ins
            </a>
        </div>

        <div>
            <div class="nav-section-title">System</div>
            <a href="{{ route('reception.help') }}" class="nav-item {{ request()->routeIs('reception.help') ? 'active' : '' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Help
            </a>
        </div>
    </nav>
</aside>

<div class="main">
    <header class="topbar">
        <div class="breadcrumb">Reception / <span>@yield('breadcrumb', 'Dashboard')</span></div>
        <div class="topbar-right">
            @php
                $__recName     = Auth::user()->name;
                $__recInitials = collect(explode(' ', $__recName))
                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                    ->take(2)->implode('');
            @endphp
            <div class="user-chip">
                <div class="avatar">{{ $__recInitials }}</div>
                <div>
                    <div class="user-chip-name">{{ $__recName }}</div>
                    <div class="user-chip-role">Reception</div>
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
