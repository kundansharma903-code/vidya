@extends('layouts.admin')

@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@section('content')

@php
    $tabs = [
        'profile'       => 'Institute Profile',
        'notifications' => 'Notifications',
        'ai'            => 'AI Settings',
    ];
@endphp

{{-- Page header --}}
<div style="margin-bottom:28px;">
    <h1 style="font-size:24px;font-weight:700;color:#f5f1e8;letter-spacing:-0.48px;margin:0 0 4px 0;">Settings</h1>
    <p style="font-size:14px;color:#a8a39c;margin:0;">Manage your institute profile, notifications, and AI configuration.</p>
</div>

@if (session('success'))
    <div style="background:rgba(127,182,133,0.12);border:1px solid rgba(127,182,133,0.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#7fb685" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span style="font-size:13px;color:#7fb685;">{{ session('success') }}</span>
    </div>
@endif

{{-- Tabs --}}
<div style="display:flex;gap:0;border-bottom:1px solid rgba(245,241,232,0.08);margin-bottom:28px;">
    @foreach ($tabs as $key => $label)
        <a href="{{ route('admin.settings', ['tab' => $key]) }}"
           style="padding:10px 20px;font-size:13px;font-weight:500;text-decoration:none;border-bottom:2px solid {{ $tab === $key ? '#7a95c8' : 'transparent' }};color:{{ $tab === $key ? '#f5f1e8' : '#a8a39c' }};background:{{ $tab === $key ? 'rgba(122,149,200,0.08)' : 'transparent' }};transition:color 0.15s;margin-bottom:-1px;">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- ==================== PROFILE TAB ==================== --}}
@if ($tab === 'profile')
<form method="POST" action="{{ route('admin.settings.profile') }}" enctype="multipart/form-data">
@csrf
@method('POST')

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

    {{-- Left column --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Institute Information --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
            <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 20px 0;">Institute Information</h2>

            @if ($errors->any())
                <div style="background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    @foreach ($errors->all() as $e)
                        <p style="font-size:12px;color:#e05252;margin:2px 0;">{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">INSTITUTE NAME *</label>
                    <input type="text" name="name" value="{{ old('name', $institute->name ?? '') }}" required
                           style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                           onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">INSTITUTE CODE</label>
                    <input type="text" value="{{ $institute->code ?? '' }}" disabled
                           style="width:100%;background:#0d0d12;border:1px solid rgba(245,241,232,0.06);border-radius:6px;padding:9px 12px;font-size:13px;color:#6a665f;outline:none;box-sizing:border-box;cursor:not-allowed;">
                    <p style="font-size:11px;color:#6a665f;margin:4px 0 0 0;">Code is set at creation and cannot be changed.</p>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">TIMEZONE</label>
                    <select name="timezone" style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                            onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                        @php $tz = old('timezone', $institute->timezone ?? 'Asia/Kolkata'); @endphp
                        <option value="Asia/Kolkata"  {{ $tz === 'Asia/Kolkata'  ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                        <option value="Asia/Colombo"  {{ $tz === 'Asia/Colombo'  ? 'selected' : '' }}>Asia/Colombo (LKT)</option>
                        <option value="Asia/Dhaka"    {{ $tz === 'Asia/Dhaka'    ? 'selected' : '' }}>Asia/Dhaka (BST)</option>
                        <option value="UTC"           {{ $tz === 'UTC'           ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Contact Details --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
            <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 20px 0;">Contact Details</h2>
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">ADDRESS</label>
                    <textarea name="address" rows="2" style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;resize:vertical;font-family:inherit;"
                              onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">{{ old('address', $institute->address ?? '') }}</textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">PHONE</label>
                        <input type="text" name="phone" value="{{ old('phone', $institute->phone ?? '') }}"
                               style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">EMAIL</label>
                        <input type="email" name="email" value="{{ old('email', $institute->email ?? '') }}"
                               style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    </div>
                    <div style="grid-column:span 2;">
                        <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">WEBSITE</label>
                        <input type="text" name="website" value="{{ old('website', $institute->website ?? '') }}" placeholder="https://"
                               style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    </div>
                </div>
            </div>
        </div>

        {{-- Academic Settings --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
            <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 20px 0;">Academic Settings</h2>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">ACADEMIC YEAR FORMAT</label>
                <select name="academic_year_format" style="width:280px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;"
                        onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    @php $ayf = old('academic_year_format', $settings->academic_year_format ?? 'YYYY-YYYY'); @endphp
                    <option value="YYYY-YYYY" {{ $ayf === 'YYYY-YYYY' ? 'selected' : '' }}>2024-2025 (YYYY-YYYY)</option>
                    <option value="YYYY-YY"   {{ $ayf === 'YYYY-YY'   ? 'selected' : '' }}>2024-25 (YYYY-YY)</option>
                    <option value="YYYY"      {{ $ayf === 'YYYY'      ? 'selected' : '' }}>2024 (YYYY)</option>
                </select>
            </div>
        </div>

        {{-- Logo Upload --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
            <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 20px 0;">Institute Logo</h2>
            <div style="display:flex;align-items:center;gap:20px;">
                @if (!empty($institute->logo_path))
                    <img src="{{ asset('storage/' . $institute->logo_path) }}" alt="Logo" style="width:64px;height:64px;object-fit:contain;border-radius:8px;background:#0f0f14;border:1px solid rgba(245,241,232,0.08);">
                @else
                    <div style="width:64px;height:64px;background:#0f0f14;border:1px dashed rgba(245,241,232,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#6a665f" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <input type="file" name="logo" accept="image/*" id="logoInput" style="display:none;" onchange="previewLogo(this)">
                    <button type="button" onclick="document.getElementById('logoInput').click()"
                            style="background:rgba(122,149,200,0.12);border:1px solid rgba(122,149,200,0.25);border-radius:6px;padding:8px 16px;font-size:12px;font-weight:500;color:#7a95c8;cursor:pointer;">
                        Upload Logo
                    </button>
                    <p style="font-size:11px;color:#6a665f;margin:6px 0 0 0;">PNG or JPG, max 2MB. Recommended: 200×200px.</p>
                </div>
            </div>
        </div>

        <div>
            <button type="submit" style="background:#7a95c8;border:none;border-radius:6px;padding:10px 24px;font-size:13px;font-weight:600;color:#08080a;cursor:pointer;">
                Save Profile
            </button>
        </div>

    </div>

    {{-- Right column — Subscription card --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;position:sticky;top:80px;">
        <h2 style="font-size:13px;font-weight:600;color:#a8a39c;letter-spacing:0.52px;text-transform:uppercase;margin:0 0 16px 0;">Subscription</h2>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <span style="font-size:22px;font-weight:700;color:#f5f1e8;letter-spacing:-0.44px;">{{ ucfirst($institute->subscription_tier ?? 'trial') }}</span>
            @php
                $tierColor = match($institute->subscription_tier ?? 'trial') {
                    'enterprise' => '#c9a55e',
                    'pro'        => '#7a95c8',
                    'basic'      => '#7fb685',
                    default      => '#a8a39c',
                };
            @endphp
            <span style="font-size:10px;font-weight:600;background:{{ $tierColor }}22;color:{{ $tierColor }};border:1px solid {{ $tierColor }}44;border-radius:4px;padding:3px 8px;letter-spacing:0.6px;text-transform:uppercase;">
                {{ strtoupper($institute->subscription_tier ?? 'TRIAL') }}
            </span>
        </div>
        <div style="font-size:13px;color:#a8a39c;margin-bottom:6px;display:flex;justify-content:space-between;">
            <span>Active Students</span>
            <span style="color:#f5f1e8;">{{ number_format($studentCount) }} / {{ number_format($institute->student_limit ?? 500) }}</span>
        </div>
        @php $pct = min(100, round(($studentCount / max(1, $institute->student_limit ?? 500)) * 100)); @endphp
        <div style="background:#0f0f14;border-radius:4px;height:6px;margin-bottom:16px;">
            <div style="width:{{ $pct }}%;background:{{ $pct >= 90 ? '#e05252' : ($pct >= 70 ? '#e0a352' : '#7fb685') }};height:6px;border-radius:4px;"></div>
        </div>
        <p style="font-size:11px;color:#6a665f;margin:0 0 16px 0;">{{ $pct }}% of student limit used.</p>
        <a href="#" style="display:block;text-align:center;background:rgba(122,149,200,0.10);border:1px solid rgba(122,149,200,0.20);border-radius:6px;padding:9px;font-size:12px;font-weight:500;color:#7a95c8;text-decoration:none;">
            Upgrade Plan
        </a>
    </div>

</div>
</form>
@endif

{{-- ==================== NOTIFICATIONS TAB ==================== --}}
@if ($tab === 'notifications')
<form method="POST" action="{{ route('admin.settings.notifications') }}">
@csrf
@method('POST')

<div style="display:flex;flex-direction:column;gap:20px;max-width:720px;">

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
        <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 4px 0;">SMTP Configuration</h2>
        <p style="font-size:12px;color:#a8a39c;margin:0 0 20px 0;">Configure outgoing email for automated notifications.</p>

        @if ($errors->any())
            <div style="background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                @foreach ($errors->all() as $e)
                    <p style="font-size:12px;color:#e05252;margin:2px 0;">{{ $e }}</p>
                @endforeach
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 140px;gap:14px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">SMTP HOST</label>
                <input type="text" name="smtp_host" value="{{ old('smtp_host', $settings->smtp_host ?? '') }}" placeholder="smtp.gmail.com"
                       style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">PORT</label>
                <input type="number" name="smtp_port" value="{{ old('smtp_port', $settings->smtp_port ?? 587) }}" min="1" max="65535"
                       style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">USERNAME</label>
                <input type="text" name="smtp_username" value="{{ old('smtp_username', $settings->smtp_username ?? '') }}" autocomplete="off"
                       style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">PASSWORD</label>
                <input type="password" name="smtp_password" value="{{ old('smtp_password', $settings->smtp_password ?? '') }}" autocomplete="new-password"
                       style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            </div>
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">ENCRYPTION</label>
            <select name="smtp_encryption" style="width:180px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;"
                    onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                @php $enc = old('smtp_encryption', $settings->smtp_encryption ?? 'tls'); @endphp
                <option value="tls"  {{ $enc === 'tls'  ? 'selected' : '' }}>TLS</option>
                <option value="ssl"  {{ $enc === 'ssl'  ? 'selected' : '' }}>SSL</option>
                <option value="none" {{ $enc === 'none' ? 'selected' : '' }}>None</option>
            </select>
        </div>
    </div>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
        <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 20px 0;">Notification Rules</h2>

        <div style="display:flex;flex-direction:column;gap:16px;">
            {{-- Email toggle --}}
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
                <div>
                    <div style="font-size:13px;font-weight:500;color:#f5f1e8;">Enable Email Notifications</div>
                    <div style="font-size:12px;color:#6a665f;margin-top:2px;">Send automated emails via SMTP above.</div>
                </div>
                <div style="position:relative;">
                    <input type="checkbox" name="notification_email_enabled" id="emailToggle" {{ old('notification_email_enabled', $settings->notification_email_enabled ?? false) ? 'checked' : '' }}
                           style="display:none;" onchange="syncToggle('emailToggle','emailTrack')">
                    <div id="emailTrack" onclick="document.getElementById('emailToggle').click()"
                         style="width:42px;height:24px;border-radius:12px;cursor:pointer;transition:background 0.2s;background:{{ old('notification_email_enabled', $settings->notification_email_enabled ?? false) ? '#7a95c8' : '#1a1a24' }};border:1px solid rgba(245,241,232,0.12);position:relative;">
                        <div id="emailThumb" style="position:absolute;top:3px;left:{{ old('notification_email_enabled', $settings->notification_email_enabled ?? false) ? '19px' : '3px' }};width:16px;height:16px;background:#f5f1e8;border-radius:50%;transition:left 0.2s;"></div>
                    </div>
                </div>
            </label>
            {{-- Weekly digest toggle --}}
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
                <div>
                    <div style="font-size:13px;font-weight:500;color:#f5f1e8;">Weekly Digest Email</div>
                    <div style="font-size:12px;color:#6a665f;margin-top:2px;">Summary report sent every Monday morning.</div>
                </div>
                <div style="position:relative;">
                    <input type="checkbox" name="weekly_digest_enabled" id="digestToggle" {{ old('weekly_digest_enabled', $settings->weekly_digest_enabled ?? true) ? 'checked' : '' }}
                           style="display:none;" onchange="syncToggle('digestToggle','digestTrack')">
                    <div id="digestTrack" onclick="document.getElementById('digestToggle').click()"
                         style="width:42px;height:24px;border-radius:12px;cursor:pointer;transition:background 0.2s;background:{{ old('weekly_digest_enabled', $settings->weekly_digest_enabled ?? true) ? '#7a95c8' : '#1a1a24' }};border:1px solid rgba(245,241,232,0.12);position:relative;">
                        <div id="digestThumb" style="position:absolute;top:3px;left:{{ old('weekly_digest_enabled', $settings->weekly_digest_enabled ?? true) ? '19px' : '3px' }};width:16px;height:16px;background:#f5f1e8;border-radius:50%;transition:left 0.2s;"></div>
                    </div>
                </div>
            </label>
            {{-- At-risk threshold --}}
            <div style="padding-top:4px;border-top:1px solid rgba(245,241,232,0.06);">
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">AT-RISK THRESHOLD (%)</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="number" name="at_risk_threshold" value="{{ old('at_risk_threshold', $settings->at_risk_threshold ?? 40) }}" min="0" max="100" step="1"
                           style="width:100px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;"
                           onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    <span style="font-size:12px;color:#6a665f;">Students below this score are flagged as at-risk.</span>
                </div>
            </div>
        </div>
    </div>

    <div>
        <button type="submit" style="background:#7a95c8;border:none;border-radius:6px;padding:10px 24px;font-size:13px;font-weight:600;color:#08080a;cursor:pointer;">
            Save Notification Settings
        </button>
    </div>
</div>
</form>
@endif

{{-- ==================== AI TAB ==================== --}}
@if ($tab === 'ai')
<form method="POST" action="{{ route('admin.settings.ai') }}">
@csrf
@method('POST')

<div style="display:flex;flex-direction:column;gap:20px;max-width:720px;">

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <div style="width:32px;height:32px;background:rgba(122,149,200,0.12);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#7a95c8" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0;">Gemini AI Integration</h2>
        </div>
        <p style="font-size:12px;color:#a8a39c;margin:0 0 20px 0;">Used for AI-generated feedback on student performance reports.</p>

        @if ($errors->any())
            <div style="background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                @foreach ($errors->all() as $e)
                    <p style="font-size:12px;color:#e05252;margin:2px 0;">{{ $e }}</p>
                @endforeach
            </div>
        @endif

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">GEMINI API KEY</label>
                <input type="password" name="gemini_api_key" value="{{ old('gemini_api_key', $settings->gemini_api_key ?? '') }}"
                       placeholder="AIza..." autocomplete="new-password"
                       style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:monospace;"
                       onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                <p style="font-size:11px;color:#6a665f;margin:4px 0 0 0;">Get your key from <span style="color:#7a95c8;">Google AI Studio</span>. Key is stored encrypted.</p>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">AI MODE</label>
                <select name="ai_mode" style="width:260px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;"
                        onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    @php $mode = old('ai_mode', $settings->ai_mode ?? 'auto'); @endphp
                    <option value="auto"          {{ $mode === 'auto'          ? 'selected' : '' }}>Auto (Gemini → Template fallback)</option>
                    <option value="gemini_only"   {{ $mode === 'gemini_only'   ? 'selected' : '' }}>Gemini Only</option>
                    <option value="template_only" {{ $mode === 'template_only' ? 'selected' : '' }}>Template Only (No AI)</option>
                </select>
                <p style="font-size:11px;color:#6a665f;margin:4px 0 0 0;">Auto mode uses Gemini and falls back to templates on error or quota limit.</p>
            </div>
        </div>
    </div>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;">
        <h2 style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 4px 0;">Performance &amp; Reliability</h2>
        <p style="font-size:12px;color:#a8a39c;margin:0 0 20px 0;">Circuit breaker prevents cascading failures; cache reduces API calls.</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">CIRCUIT BREAKER THRESHOLD</label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="number" name="ai_circuit_breaker_threshold" value="{{ old('ai_circuit_breaker_threshold', $settings->ai_circuit_breaker_threshold ?? 5) }}" min="1" max="100"
                           style="width:80px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;"
                           onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    <span style="font-size:12px;color:#6a665f;">consecutive errors</span>
                </div>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:#a8a39c;margin-bottom:6px;letter-spacing:0.3px;">CACHE TTL</label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="number" name="ai_cache_ttl" value="{{ old('ai_cache_ttl', $settings->ai_cache_ttl ?? 3600) }}" min="60" max="86400"
                           style="width:100px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:9px 12px;font-size:13px;color:#f5f1e8;outline:none;"
                           onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    <span style="font-size:12px;color:#6a665f;">seconds</span>
                </div>
            </div>
        </div>
    </div>

    <div>
        <button type="submit" style="background:#7a95c8;border:none;border-radius:6px;padding:10px 24px;font-size:13px;font-weight:600;color:#08080a;cursor:pointer;">
            Save AI Settings
        </button>
    </div>
</div>
</form>
@endif

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var imgs = document.querySelectorAll('img[alt="Logo"]');
            if (imgs.length) { imgs[0].src = e.target.result; }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function syncToggle(checkId, trackId) {
    var cb = document.getElementById(checkId);
    var track = document.getElementById(trackId);
    var thumb = track.querySelector('div');
    if (cb.checked) {
        track.style.background = '#7a95c8';
        thumb.style.left = '19px';
    } else {
        track.style.background = '#1a1a24';
        thumb.style.left = '3px';
    }
}
</script>

@endsection
