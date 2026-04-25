@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<div class="w-[440px] rounded-[12px] px-[48px] pt-[48px] pb-[40px] flex flex-col gap-[28px] items-center shadow-[0px_8px_32px_0px_rgba(0,0,0,0.5)]"
     style="background-color: #14141b; border: 1px solid rgba(245,241,232,0.08);">

    {{-- Logo --}}
    <div class="flex flex-col items-center gap-[12px]">
        <span class="text-[36px] font-bold tracking-[-0.72px]" style="color: #f5f1e8;">Vidya</span>
        <div class="h-[2px] w-[32px] rounded-[1px]" style="background-color: #7a95c8;"></div>
    </div>

    {{-- Heading --}}
    <div class="flex flex-col items-center gap-[6px] text-center">
        <p class="text-[20px] font-semibold tracking-[-0.2px]" style="color: #f5f1e8;">Welcome back</p>
        <p class="text-[13px] leading-[1.5]" style="color: #a8a39c;">Sign in to access your institute dashboard</p>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
    <div class="w-full rounded-[6px] px-[14px] py-[10px] text-[13px]"
         style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-[18px] w-full">
        @csrf

        {{-- Email or Username --}}
        <div class="flex flex-col gap-[8px]">
            <label class="text-[11px] font-medium tracking-[0.88px] uppercase" style="color: #a8a39c;">
                Email or Username
            </label>
            <input
                type="text"
                name="login"
                value="{{ old('login') }}"
                placeholder="admin@abccoaching.in"
                autocomplete="username"
                required
                class="h-[44px] w-full rounded-[6px] px-[14px] text-[14px] outline-none transition-colors placeholder:text-[#6a665f] focus:border-[#7a95c8]"
                style="background-color: #0f0f14; border: 1px solid rgba(245,241,232,0.10); color: #f5f1e8;"
            >
        </div>

        {{-- Password --}}
        <div class="flex flex-col gap-[8px]">
            <div class="flex items-center justify-between">
                <label class="text-[11px] font-medium tracking-[0.88px] uppercase" style="color: #a8a39c;">
                    Password
                </label>
                <a href="{{ route('password.request') }}" class="text-[12px] font-medium transition-opacity hover:opacity-70"
                   style="color: #7a95c8;">Forgot?</a>
            </div>
            <input
                type="password"
                name="password"
                placeholder="••••••••••••"
                autocomplete="current-password"
                required
                class="h-[44px] w-full rounded-[6px] px-[14px] text-[14px] outline-none transition-colors placeholder:text-[#6a665f] focus:border-[#7a95c8]"
                style="background-color: #0f0f14; border: 1px solid rgba(245,241,232,0.10); color: #f5f1e8;"
            >
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-[10px]">
            <input
                type="checkbox"
                name="remember"
                id="remember"
                class="size-[16px] rounded-[3px] cursor-pointer appearance-none checked:bg-[#7a95c8]"
                style="background-color: #0f0f14; border: 1px solid rgba(245,241,232,0.20);"
            >
            <label for="remember" class="text-[13px] cursor-pointer select-none" style="color: #a8a39c;">
                Keep me signed in
            </label>
        </div>

        {{-- Sign In Button --}}
        <button
            type="submit"
            class="h-[44px] w-full rounded-[6px] text-[14px] font-semibold transition-opacity hover:opacity-90 active:opacity-80 shadow-[0px_2px_8px_0px_rgba(122,149,200,0.2)]"
            style="background-color: #7a95c8; color: #14141b;"
        >
            Sign in
        </button>

    </form>

    {{-- Footer note --}}
    <p class="text-[12px] text-center" style="color: #6a665f;">
        Need help? Contact your administrator
    </p>

</div>
@endsection
