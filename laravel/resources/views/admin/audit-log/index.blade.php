@extends('layouts.admin')

@section('title', 'Audit Log')
@section('breadcrumb', 'Audit Log')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="font-size:24px;font-weight:700;color:#f5f1e8;letter-spacing:-0.48px;margin:0 0 4px 0;">Audit Log</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">Immutable record of all system and user activity.</p>
    </div>
    <button onclick="alert('Export coming soon')"
            style="display:flex;align-items:center;gap:8px;background:#14141b;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 16px;font-size:13px;font-weight:500;color:#a8a39c;cursor:pointer;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export Excel
    </button>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
    @php
        $statCards = [
            ['label' => 'TOTAL ENTRIES',  'value' => number_format($stats['total']),  'color' => '#7a95c8'],
            ['label' => 'TODAY',          'value' => number_format($stats['today']),  'color' => '#7fb685'],
            ['label' => 'ERRORS (1H)',    'value' => number_format($stats['errors']), 'color' => '#e05252'],
        ];
    @endphp
    @foreach ($statCards as $sc)
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 20px;">
        <div style="font-size:10px;font-weight:600;color:#6a665f;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">{{ $sc['label'] }}</div>
        <div style="font-size:26px;font-weight:700;color:{{ $sc['color'] }};letter-spacing:-0.5px;">{{ $sc['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.audit-log') }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description, entity, user..."
           style="flex:1;min-width:220px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:8px 12px;font-size:13px;color:#f5f1e8;outline:none;"
           onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    <select name="user_id" style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:8px 12px;font-size:13px;color:{{ request('user_id') ? '#f5f1e8' : '#6a665f' }};outline:none;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        <option value="">All Users</option>
        @foreach ($users as $u)
            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
    </select>
    <select name="action" style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:8px 12px;font-size:13px;color:{{ request('action') ? '#f5f1e8' : '#6a665f' }};outline:none;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        <option value="">All Actions</option>
        <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
        <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
        <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
        <option value="login"  {{ request('action') === 'login'  ? 'selected' : '' }}>Login</option>
        <option value="import" {{ request('action') === 'import' ? 'selected' : '' }}>Import</option>
        <option value="export" {{ request('action') === 'export' ? 'selected' : '' }}>Export</option>
        <option value="error"  {{ request('action') === 'error'  ? 'selected' : '' }}>Error</option>
    </select>
    <select name="period" style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:8px 12px;font-size:13px;color:{{ request('period') ? '#f5f1e8' : '#6a665f' }};outline:none;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        <option value="">All Time</option>
        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
        <option value="week"  {{ request('period') === 'week'  ? 'selected' : '' }}>Last 7 Days</option>
        <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
    </select>
    <button type="submit" style="background:#7a95c8;border:none;border-radius:6px;padding:8px 18px;font-size:13px;font-weight:600;color:#08080a;cursor:pointer;">Filter</button>
    @if (request()->hasAny(['search','user_id','action','period']))
        <a href="{{ route('admin.audit-log') }}" style="font-size:12px;color:#a8a39c;text-decoration:none;padding:8px 4px;">Clear</a>
    @endif
</form>

{{-- Table --}}
<div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
    @if ($logs->isEmpty())
        <div style="padding:64px 32px;text-align:center;">
            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#6a665f" stroke-width="1.25" style="margin:0 auto 12px auto;display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p style="font-size:14px;color:#6a665f;margin:0;">No log entries found.</p>
        </div>
    @else
        {{-- Table head --}}
        <div style="display:grid;grid-template-columns:170px 1fr 140px 280px 130px 32px;gap:0;border-bottom:1px solid rgba(245,241,232,0.06);padding:0 16px;">
            @foreach (['TIMESTAMP','USER','ACTION','ENTITY','IP ADDRESS',''] as $col)
            <div style="padding:10px 10px;font-size:10px;font-weight:600;color:#6a665f;letter-spacing:1px;text-transform:uppercase;">{{ $col }}</div>
            @endforeach
        </div>

        @foreach ($logs as $log)
        @php
            $prefix = explode('.', $log->action)[0] ?? '';
            $actionColor = match($prefix) {
                'create', 'register' => '#7fb685',
                'update'             => '#7a95c8',
                'delete', 'error'    => '#e05252',
                'import', 'upload'   => '#e0a352',
                'export'             => '#a880e0',
                default              => '#a8a39c',
            };
            $actionBg = $actionColor . '18';
        @endphp
        <div style="display:grid;grid-template-columns:170px 1fr 140px 280px 130px 32px;gap:0;border-bottom:1px solid rgba(245,241,232,0.04);padding:0 16px;align-items:center;min-height:52px;"
             onmouseover="this.style.background='rgba(26,26,36,0.7)'" onmouseout="this.style.background=''">

            {{-- Timestamp --}}
            <div style="padding:0 10px;">
                <div style="font-size:12px;color:#f5f1e8;font-variant-numeric:tabular-nums;">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                <div style="font-size:11px;color:#6a665f;font-variant-numeric:tabular-nums;">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</div>
            </div>

            {{-- User --}}
            <div style="padding:0 10px;display:flex;align-items:center;gap:10px;min-width:0;">
                @if ($log->user_name)
                    @php $initials = collect(explode(' ', $log->user_name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join(''); @endphp
                    <div style="width:28px;height:28px;background:#1a1a24;border:1px solid rgba(245,241,232,0.08);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:10px;font-weight:600;color:#a8a39c;">{{ $initials }}</span>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:12px;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->user_name }}</div>
                        <span style="font-size:10px;background:rgba(122,149,200,0.12);color:#7a95c8;border-radius:3px;padding:1px 6px;display:inline-block;margin-top:2px;">{{ ucfirst($log->user_role ?? '') }}</span>
                    </div>
                @else
                    <div style="width:28px;height:28px;background:#1a1a24;border:1px solid rgba(245,241,232,0.06);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#6a665f" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <span style="font-size:12px;color:#6a665f;font-style:italic;">System</span>
                @endif
            </div>

            {{-- Action badge --}}
            <div style="padding:0 10px;">
                <span style="font-size:11px;font-weight:600;background:{{ $actionBg }};color:{{ $actionColor }};border:1px solid {{ $actionColor }}33;border-radius:4px;padding:3px 9px;letter-spacing:0.4px;text-transform:uppercase;white-space:nowrap;">
                    {{ str_replace('.', ' › ', $log->action) }}
                </span>
            </div>

            {{-- Entity --}}
            <div style="padding:0 10px;min-width:0;">
                @if ($log->entity_type)
                    <span style="font-size:12px;color:#a8a39c;background:rgba(245,241,232,0.04);border-radius:4px;padding:2px 8px;font-family:monospace;">{{ $log->entity_type }}{{ $log->entity_id ? ' #' . $log->entity_id : '' }}</span>
                @endif
                @if ($log->description)
                    <div style="font-size:12px;color:#6a665f;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->description }}</div>
                @endif
            </div>

            {{-- IP --}}
            <div style="padding:0 10px;">
                <span style="font-size:11px;color:#6a665f;font-family:monospace;">{{ $log->ip_address ?? '—' }}</span>
            </div>

            {{-- More --}}
            <div style="padding:0 4px;display:flex;align-items:center;justify-content:center;">
                @if ($log->changes || $log->metadata)
                    <button onclick='showLogDetail({{ json_encode(["action"=>$log->action,"description"=>$log->description,"changes"=>$log->changes,"metadata"=>$log->metadata,"ip"=>$log->ip_address,"ua"=>$log->user_agent]) }})'
                            title="View details"
                            style="background:none;border:none;cursor:pointer;color:#6a665f;padding:4px;border-radius:4px;"
                            onmouseover="this.style.color='#a8a39c'" onmouseout="this.style.color='#6a665f'">•••</button>
                @endif
            </div>
        </div>
        @endforeach
    @endif
</div>

{{-- Pagination --}}
@if ($logs->hasPages())
<div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
    <span style="font-size:12px;color:#6a665f;">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries</span>
    <div style="display:flex;gap:4px;">
        @if ($logs->onFirstPage())
            <span style="padding:6px 12px;font-size:12px;color:#6a665f;background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:5px;">← Prev</span>
        @else
            <a href="{{ $logs->previousPageUrl() }}" style="padding:6px 12px;font-size:12px;color:#a8a39c;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:5px;text-decoration:none;">← Prev</a>
        @endif
        @foreach ($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" style="padding:6px 10px;font-size:12px;{{ $page == $logs->currentPage() ? 'background:#7a95c8;color:#08080a;font-weight:600;' : 'background:#14141b;color:#a8a39c;' }}border:1px solid rgba(245,241,232,0.08);border-radius:5px;text-decoration:none;">{{ $page }}</a>
        @endforeach
        @if ($logs->hasMorePages())
            <a href="{{ $logs->nextPageUrl() }}" style="padding:6px 12px;font-size:12px;color:#a8a39c;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:5px;text-decoration:none;">Next →</a>
        @else
            <span style="padding:6px 12px;font-size:12px;color:#6a665f;background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:5px;">Next →</span>
        @endif
    </div>
</div>
@endif

{{-- Detail Modal --}}
<div id="logDetailModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.85);z-index:100;display:none;align-items:center;justify-content:center;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.10);border-radius:12px;width:560px;max-width:90vw;max-height:80vh;overflow-y:auto;padding:24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0;">Log Detail</h3>
            <button onclick="closeLogDetail()" style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:18px;line-height:1;" onmouseover="this.style.color='#a8a39c'" onmouseout="this.style.color='#6a665f'">×</button>
        </div>
        <pre id="logDetailContent" style="font-size:12px;color:#a8a39c;background:#0f0f14;border-radius:6px;padding:16px;overflow-x:auto;white-space:pre-wrap;word-break:break-all;margin:0;font-family:monospace;line-height:1.6;"></pre>
    </div>
</div>

<script>
function showLogDetail(data) {
    document.getElementById('logDetailContent').textContent = JSON.stringify(data, null, 2);
    document.getElementById('logDetailModal').style.display = 'flex';
}
function closeLogDetail() {
    document.getElementById('logDetailModal').style.display = 'none';
}
document.getElementById('logDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeLogDetail();
});
</script>

@endsection
