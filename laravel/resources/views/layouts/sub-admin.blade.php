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
    $authUser     = Auth::user();
    $initials     = collect(explode(' ', $authUser->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');

    $navSections = [
        ''         => [
            ['name' => 'sub-admin.dashboard',  'label' => 'Dashboard'],
        ],
        'TESTS'    => [
            ['name' => 'sub-admin.tests.create', 'label' => 'Create Test'],
            ['name' => 'sub-admin.tests.index',  'label' => 'All Tests'],
        ],
        'RESULTS'  => [
            ['name' => 'sub-admin.results.upload',  'label' => 'Upload Results'],
            ['name' => 'sub-admin.results.history', 'label' => 'Upload History'],
        ],
        'STUDENTS' => [
            ['name' => 'sub-admin.students', 'label' => 'Students'],
        ],
        'SYSTEM'   => [
            ['name' => 'sub-admin.help', 'label' => 'Help'],
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
        <div>
            <span style="font-size:22px;font-weight:700;color:#f5f1e8;letter-spacing:-0.44px;">Vidya</span>
            <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:#7a95c8;margin-left:2px;align-self:flex-start;margin-top:12px;position:relative;top:-10px;"></span>
            <div style="width:20px;height:2px;background:#7a95c8;border-radius:1px;margin-top:-4px;"></div>
        </div>
    </div>

    {{-- Nav --}}
    <nav style="flex:1;padding:16px 0;">
        @foreach ($navSections as $section => $items)
            @if ($section !== '')
                <div style="padding:0 20px;margin-bottom:8px;margin-top:16px;">
                    <span style="font-size:10px;font-weight:600;color:#6a665f;letter-spacing:1.2px;text-transform:uppercase;">{{ $section }}</span>
                </div>
            @else
                <div style="margin-bottom:4px;"></div>
            @endif
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
                    <span style="font-size:13px;font-weight:{{ $active ? '500' : '400' }};color:{{ $active ? '#f5f1e8' : '#a8a39c' }};">{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endforeach
    </nav>

</aside>

{{-- Main area --}}
<div style="margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh;">

    {{-- Topbar --}}
    <header style="height:60px;background:#08080a;border-bottom:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:space-between;padding:0 24px 0 32px;position:sticky;top:0;z-index:40;flex-shrink:0;">

        {{-- Breadcrumb --}}
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;color:#6a665f;">Sub-Admin</span>
            <span style="font-size:13px;color:#6a665f;">/</span>
            <span style="font-size:14px;font-weight:600;color:#f5f1e8;">@yield('breadcrumb', 'Dashboard')</span>
        </div>

        {{-- Right side --}}
        <div style="display:flex;align-items:center;gap:12px;">

            {{-- Bell --}}
            <div style="width:36px;height:36px;background:#14141b;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;color:#a8a39c;">
                🔔
            </div>

            {{-- User chip --}}
            <div style="display:flex;align-items:center;gap:10px;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:18px;padding:4px 12px 4px 4px;">
                <div style="width:28px;height:28px;background:#c89a6a;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="font-size:10px;font-weight:600;color:#f5f1e8;">{{ $initials }}</span>
                </div>
                <div style="display:flex;flex-direction:column;line-height:1.25;">
                    <span style="font-size:12px;font-weight:500;color:#f5f1e8;">{{ $authUser->name }}</span>
                    <span style="font-size:10px;color:#6a665f;">Sub-Admin · {{ $currentInstitute->name ?? '' }}</span>
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
