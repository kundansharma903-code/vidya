<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Vidya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="margin:0;padding:0;min-height:100vh;display:flex;background:#08080a;color:#f5f1e8;font-family:'Inter',ui-sans-serif,system-ui,sans-serif;">

@php
    use Illuminate\Support\Facades\Auth;
    $currentRoute = request()->route()->getName() ?? '';

    $navSections = [
        'SETUP' => [
            ['name' => 'admin.dashboard',  'label' => 'Dashboard',   'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zm10-5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>'],
            ['name' => 'admin.courses',    'label' => 'Courses',     'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
            ['name' => 'admin.batches',    'label' => 'Batches',     'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'],
            ['name' => 'admin.subjects',   'label' => 'Subjects',    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
            ['name' => 'admin.curriculum', 'label' => 'Curriculum',  'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
        ],
        'USERS' => [
            ['name' => 'admin.students',    'label' => 'Students',    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>'],
            ['name' => 'admin.staff',       'label' => 'Staff',       'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
            ['name' => 'admin.assignments', 'label' => 'Assignments', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
        ],
        'SYSTEM' => [
            ['name' => 'admin.settings',   'label' => 'Settings',    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
            ['name' => 'admin.audit-log',  'label' => 'Audit Log',   'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        ],
    ];

    $routeExists = function(string $name): bool {
        try { route($name); return true; } catch (\Exception $e) { return false; }
    };
@endphp

    {{-- Sidebar --}}
    <aside style="width:240px;min-height:100vh;background:#14141b;border-right:1px solid rgba(245,241,232,0.06);display:flex;flex-direction:column;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;z-index:50;overflow-y:auto;">

        {{-- Wordmark --}}
        <div style="height:60px;display:flex;align-items:center;padding:0 20px;border-bottom:1px solid rgba(245,241,232,0.06);flex-shrink:0;">
            <span style="font-size:22px;font-weight:700;color:#f5f1e8;letter-spacing:-0.44px;">Vidya</span>
            <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:#7a95c8;margin-left:2px;align-self:flex-start;margin-top:12px;"></span>
        </div>

        {{-- Nav --}}
        <nav style="flex:1;padding:16px 0;">
            @foreach ($navSections as $section => $items)
                <div style="padding:0 20px;margin-bottom:8px;{{ !$loop->first ? 'margin-top:16px;' : '' }}">
                    <span style="font-size:10px;font-weight:600;color:#6a665f;letter-spacing:1.2px;text-transform:uppercase;">{{ $section }}</span>
                </div>
                @foreach ($items as $item)
                    @php $active = $currentRoute === $item['name']; @endphp
                    @if ($routeExists($item['name']))
                        <a href="{{ route($item['name']) }}"
                    @else
                        <a href="#"
                    @endif
                       style="display:flex;align-items:center;gap:10px;height:38px;padding:0 20px;text-decoration:none;position:relative;{{ $active ? 'background:#1a1a24;' : '' }}transition:background 0.15s;"
                       onmouseover="{{ !$active ? "this.style.background='rgba(26,26,36,0.5)'" : '' }}"
                       onmouseout="{{ !$active ? "this.style.background=''" : '' }}">
                        @if ($active)
                            <span style="position:absolute;left:0;top:0;bottom:0;width:3px;background:#7a95c8;border-radius:0 2px 2px 0;"></span>
                        @endif
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $active ? '#f5f1e8' : '#a8a39c' }}" stroke-width="1.75" style="flex-shrink:0;">
                            {!! $item['svg'] !!}
                        </svg>
                        <span style="font-size:13px;font-weight:{{ $active ? '500' : '400' }};color:{{ $active ? '#f5f1e8' : '#a8a39c' }};">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

    </aside>

    {{-- Main area --}}
    <div style="margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh;">

        {{-- Topbar --}}
        <header style="height:60px;background:#08080a;border-bottom:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:space-between;padding:0 32px;position:sticky;top:0;z-index:40;flex-shrink:0;">

            {{-- Breadcrumb --}}
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:13px;color:#6a665f;">Admin</span>
                <span style="font-size:13px;color:#6a665f;">/</span>
                <span style="font-size:14px;font-weight:600;color:#f5f1e8;">@yield('breadcrumb', 'Dashboard')</span>
            </div>

            {{-- Right side --}}
            <div style="display:flex;align-items:center;gap:10px;">

                {{-- Bell --}}
                @php
                    $unreadCount = \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                <a href="{{ $routeExists('admin.notifications') ? route('admin.notifications') : '#' }}"
                   style="width:36px;height:36px;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;text-decoration:none;"
                   onmouseover="this.style.borderColor='rgba(245,241,232,0.18)'" onmouseout="this.style.borderColor='rgba(245,241,232,0.08)'">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#a8a39c" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if ($unreadCount > 0)
                        <span style="position:absolute;top:7px;right:7px;width:7px;height:7px;background:#e05252;border-radius:50%;border:1.5px solid #08080a;"></span>
                    @endif
                </a>

                {{-- User chip --}}
                @php
                    $authUser = Auth::user();
                    $initials = collect(explode(' ', $authUser->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                @endphp
                <div style="display:flex;align-items:center;gap:10px;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:18px;padding:5px 12px 5px 6px;">
                    <div style="width:28px;height:28px;background:#5f7eb4;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:11px;font-weight:600;color:#f5f1e8;">{{ $initials }}</span>
                    </div>
                    <div style="display:flex;flex-direction:column;line-height:1.25;">
                        <span style="font-size:12px;font-weight:500;color:#f5f1e8;">{{ $authUser->name }}</span>
                        <span style="font-size:10px;color:#6a665f;">{{ ucfirst($authUser->role) }} · {{ $currentInstitute->name ?? '' }}</span>
                    </div>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" title="Sign out" style="background:none;border:none;cursor:pointer;padding:4px;display:flex;align-items:center;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6a665f" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>

            </div>
        </header>

        {{-- Page content --}}
        <main style="flex:1;padding:32px;">
            @yield('content')
        </main>

    </div>

</body>
</html>
