<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teacher') — Vidya</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #08080a; color: #f5f1e8; min-height: 100vh; display: flex; }
        a { text-decoration: none; color: inherit; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px;
            flex-shrink: 0;
            background: #0f0f14;
            border-right: 1px solid rgba(245,241,232,0.06);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 20px 20px 18px;
            border-bottom: 1px solid rgba(245,241,232,0.06);
        }
        .sidebar-logo .logo-mark {
            font-size: 18px;
            font-weight: 800;
            color: #f5f1e8;
            letter-spacing: -0.36px;
        }
        .sidebar-logo .logo-mark span { color: #7fb685; }
        .sidebar-logo .role-pill {
            display: inline-block;
            margin-top: 4px;
            background: rgba(127,182,133,0.12);
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 600;
            color: #7fb685;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .sidebar-section {
            padding: 18px 12px 6px;
        }
        .sidebar-section-label {
            font-size: 9px;
            font-weight: 600;
            color: #6a665f;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 0 8px;
            margin-bottom: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #a8a39c;
            transition: background 0.15s, color 0.15s;
            cursor: pointer;
        }
        .nav-item:hover { background: rgba(245,241,232,0.05); color: #f5f1e8; }
        .nav-item.active { background: rgba(127,182,133,0.12); color: #7fb685; }
        .nav-item .icon { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            margin-top: auto;
            padding: 14px 12px;
            border-top: 1px solid rgba(245,241,232,0.06);
        }
        .sidebar-footer .user-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 6px;
        }
        .sidebar-footer .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(127,182,133,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #7fb685;
            flex-shrink: 0;
        }
        .sidebar-footer .name { font-size: 12px; font-weight: 600; color: #f5f1e8; }
        .sidebar-footer .role-tag { font-size: 10px; color: #6a665f; }

        /* ── Main ── */
        .main {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            height: 56px;
            background: #0f0f14;
            border-bottom: 1px solid rgba(245,241,232,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar .breadcrumb {
            font-size: 13px;
            font-weight: 500;
            color: #a8a39c;
        }
        .topbar .breadcrumb span { color: #f5f1e8; }
        .topbar .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .topbar .avatar-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(127,182,133,0.1);
            border: 1px solid rgba(127,182,133,0.2);
            border-radius: 9999px;
            padding: 5px 12px 5px 6px;
            cursor: pointer;
        }
        .topbar .avatar-pill .av {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(127,182,133,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: #7fb685;
        }
        .topbar .avatar-pill .av-name { font-size: 12px; font-weight: 600; color: #7fb685; }

        /* ── Page content ── */
        .page-content {
            padding: 28px 32px;
            flex: 1;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">Vidya<span>.</span></div>
        <span class="role-pill">Teacher</span>
    </div>

    {{-- MY SUBJECT --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">My Subject</div>
        <a href="{{ route('teacher.dashboard') }}" class="nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
            <span class="icon">⊞</span> Dashboard
        </a>
        <a href="{{ route('teacher.students') }}" class="nav-item {{ request()->routeIs('teacher.students') ? 'active' : '' }}">
            <span class="icon">👥</span> My Students
        </a>
        <a href="{{ route('teacher.rankings') }}" class="nav-item {{ request()->routeIs('teacher.rankings') ? 'active' : '' }}">
            <span class="icon">🏅</span> Rankings
        </a>
        <a href="{{ route('teacher.heatmap') }}" class="nav-item {{ request()->routeIs('teacher.heatmap') ? 'active' : '' }}">
            <span class="icon">⊞</span> Class Heatmap
        </a>
        <a href="{{ route('teacher.insights') }}" class="nav-item {{ request()->routeIs('teacher.insights') ? 'active' : '' }}">
            <span class="icon">◎</span> Class Insights
        </a>
        <a href="{{ route('teacher.weak-topics') }}" class="nav-item {{ request()->routeIs('teacher.weak-topics') ? 'active' : '' }}">
            <span class="icon">▽</span> Weak Topics
        </a>
    </div>

    {{-- TESTS --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">Tests</div>
        <a href="{{ route('teacher.tests') }}" class="nav-item {{ request()->routeIs('teacher.tests') ? 'active' : '' }}">
            <span class="icon">✎</span> My Tests
        </a>
    </div>

    {{-- SYSTEM --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">System</div>
        <a href="{{ route('teacher.notifications') }}" class="nav-item {{ request()->routeIs('teacher.notifications') ? 'active' : '' }}">
            <span class="icon">🔔</span> Notifications
        </a>
        <a href="{{ route('teacher.help') }}" class="nav-item {{ request()->routeIs('teacher.help') ? 'active' : '' }}">
            <span class="icon">?</span> Help
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-row">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="role-tag">Teacher</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:4px;">
            @csrf
            <button type="submit" style="width:100%;background:none;border:none;text-align:left;padding:8px 10px;font-size:12px;color:#6a665f;cursor:pointer;border-radius:6px;" onmouseover="this.style.color='#c87064'" onmouseout="this.style.color='#6a665f'">
                ↩ Sign out
            </button>
        </form>
    </div>
</nav>

{{-- Main --}}
<div class="main">
    {{-- Topbar --}}
    <header class="topbar">
        <div class="breadcrumb">Teacher · <span>@yield('breadcrumb', 'Dashboard')</span></div>
        <div class="topbar-right">
            <div class="avatar-pill">
                <div class="av">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <span class="av-name">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="page-content">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
