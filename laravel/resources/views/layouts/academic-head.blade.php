<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Academic Head') — Vidya</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #08080a; color: #f5f1e8; min-height: 100vh; display: flex; }
        a { text-decoration: none; color: inherit; }

        /* ── Sidebar ── */
        .sidebar {
            width: 224px;
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
        .logo-mark { font-size: 18px; font-weight: 800; color: #f5f1e8; letter-spacing: -0.36px; }
        .logo-mark span { color: #7a95c8; }
        .role-pill {
            display: inline-block; margin-top: 4px;
            background: rgba(122,149,200,0.12); border-radius: 9999px;
            padding: 2px 8px; font-size: 10px; font-weight: 600;
            color: #7a95c8; letter-spacing: 0.6px; text-transform: uppercase;
        }
        .sidebar-section { padding: 16px 12px 4px; }
        .sidebar-section-label {
            font-size: 9px; font-weight: 600; color: #6a665f;
            letter-spacing: 1.2px; text-transform: uppercase;
            padding: 0 8px; margin-bottom: 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 6px;
            font-size: 13px; font-weight: 500; color: #a8a39c;
            transition: background 0.15s, color 0.15s;
        }
        .nav-item:hover { background: rgba(245,241,232,0.05); color: #f5f1e8; }
        .nav-item.active { background: rgba(122,149,200,0.12); color: #7a95c8; }
        .nav-item .icon { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-footer {
            margin-top: auto; padding: 14px 12px;
            border-top: 1px solid rgba(245,241,232,0.06);
        }
        .sidebar-footer .user-row {
            display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 6px;
        }
        .sidebar-footer .avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: rgba(122,149,200,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #7a95c8; flex-shrink: 0;
        }
        .sidebar-footer .name { font-size: 12px; font-weight: 600; color: #f5f1e8; }
        .sidebar-footer .role-tag { font-size: 10px; color: #6a665f; }

        /* ── Main ── */
        .main { margin-left: 224px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Topbar ── */
        .topbar {
            height: 56px; background: #0f0f14;
            border-bottom: 1px solid rgba(245,241,232,0.06);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; position: sticky; top: 0; z-index: 10;
        }
        .topbar .breadcrumb { font-size: 13px; font-weight: 500; color: #a8a39c; }
        .topbar .breadcrumb span { color: #f5f1e8; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .bell-btn {
            width: 32px; height: 32px; border-radius: 6px;
            background: rgba(245,241,232,0.04); border: 1px solid rgba(245,241,232,0.08);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; cursor: pointer; text-decoration: none;
        }
        .avatar-chip {
            display: flex; align-items: center; gap: 8px;
            background: rgba(122,149,200,0.1); border: 1px solid rgba(122,149,200,0.2);
            border-radius: 9999px; padding: 5px 12px 5px 6px;
        }
        .avatar-chip .av {
            width: 24px; height: 24px; border-radius: 50%;
            background: rgba(122,149,200,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 9px; font-weight: 700; color: #7a95c8;
        }
        .avatar-chip .av-name { font-size: 12px; font-weight: 600; color: #7a95c8; }
        .avatar-chip .av-role { font-size: 10px; color: #6a665f; }

        .page-content { padding: 28px 32px; flex: 1; }
    </style>
    @stack('styles')
</head>
<body>

<nav class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">Vidya<span>.</span></div>
        <span class="role-pill">Academic Head</span>
    </div>

    {{-- Main --}}
    <div class="sidebar-section">
        <a href="{{ route('academic-head.dashboard') }}" class="nav-item {{ request()->routeIs('academic-head.dashboard') ? 'active' : '' }}">
            <span class="icon">⊞</span> Dashboard
        </a>
    </div>

    {{-- ACADEMICS --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">Academics</div>
        <a href="{{ route('academic-head.curriculum-coverage') }}" class="nav-item {{ request()->routeIs('academic-head.curriculum-coverage') ? 'active' : '' }}">
            <span class="icon">📋</span> Curriculum Coverage
        </a>
        <a href="{{ route('academic-head.test-quality') }}" class="nav-item {{ request()->routeIs('academic-head.test-quality') ? 'active' : '' }}">
            <span class="icon">✎</span> Test Quality
        </a>
        <a href="{{ route('academic-head.subject-performance') }}" class="nav-item {{ request()->routeIs('academic-head.subject-performance') ? 'active' : '' }}">
            <span class="icon">◎</span> Subject Performance
        </a>
    </div>

    {{-- TEACHERS --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">Teachers</div>
        <a href="{{ route('academic-head.teacher-effectiveness') }}" class="nav-item {{ request()->routeIs('academic-head.teacher-effectiveness') ? 'active' : '' }}">
            <span class="icon">🏆</span> Teacher Effectiveness
        </a>
        <a href="{{ route('academic-head.teacher-assignments') }}" class="nav-item {{ request()->routeIs('academic-head.teacher-assignments') ? 'active' : '' }}">
            <span class="icon">👤</span> Teacher Assignments
        </a>
    </div>

    {{-- STUDENTS --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">Students</div>
        <a href="{{ route('academic-head.at-risk-students') }}" class="nav-item {{ request()->routeIs('academic-head.at-risk-students') ? 'active' : '' }}">
            <span class="icon">⚠</span> At-Risk Students
        </a>
    </div>

    {{-- SYSTEM --}}
    <div class="sidebar-section">
        <div class="sidebar-section-label">System</div>
        <a href="{{ route('academic-head.notifications') }}" class="nav-item {{ request()->routeIs('academic-head.notifications') ? 'active' : '' }}">
            <span class="icon">🔔</span> Notifications
        </a>
        <a href="{{ route('academic-head.help') }}" class="nav-item {{ request()->routeIs('academic-head.help') ? 'active' : '' }}">
            <span class="icon">?</span> Help
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-row">
            <div class="avatar">{{ collect(explode(' ', Auth::user()->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}</div>
            <div>
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="role-tag">Academic Head</div>
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

<div class="main">
    <header class="topbar">
        <div class="breadcrumb">Academic Head · <span>@yield('breadcrumb', 'Dashboard')</span></div>
        <div class="topbar-right">
            <a href="{{ route('academic-head.notifications') }}" class="bell-btn" title="Notifications">🔔</a>
            @php
                $__ahName     = Auth::user()->name;
                $__ahInitials = collect(explode(' ', $__ahName))
                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                    ->take(2)->implode('');
            @endphp
            <div class="avatar-chip">
                <div class="av">{{ $__ahInitials }}</div>
                <div>
                    <div class="av-name">{{ $__ahName }}</div>
                </div>
            </div>
        </div>
    </header>

    <main class="page-content">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
