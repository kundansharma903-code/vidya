@extends('layouts.auth')
@section('title', 'Forgot Password')

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
        <p class="text-[20px] font-semibold tracking-[-0.2px]" style="color: #f5f1e8;">Reset your password</p>
        <p class="text-[13px] leading-[1.5]" style="color: #a8a39c;">
            Enter your email and we'll send you a reset link
        </p>
    </div>

    {{-- Status --}}
    @if (session('status'))
    <div class="w-full rounded-[6px] px-[14px] py-[10px] text-[13px]"
         style="background-color: rgba(74,222,128,0.1); border: 1px solid rgba(74,222,128,0.3); color: #4ade80;">
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="w-full rounded-[6px] px-[14px] py-[10px] text-[13px]"
         style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-[18px] w-full">
        @csrf
        <div class="flex flex-col gap-[8px]">
            <label class="text-[11px] font-medium tracking-[0.88px] uppercase" style="color: #a8a39c;">
                Email Address
            </label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="admin@abccoaching.in"
                required
                class="h-[44px] w-full rounded-[6px] px-[14px] text-[14px] outline-none placeholder:text-[#6a665f] focus:border-[#7a95c8]"
                style="background-color: #0f0f14; border: 1px solid rgba(245,241,232,0.10); color: #f5f1e8;"
            >
        </div>

        <button
            type="submit"
            class="h-[44px] w-full rounded-[6px] text-[14px] font-semibold transition-opacity hover:opacity-90 shadow-[0px_2px_8px_0px_rgba(122,149,200,0.2)]"
            style="background-color: #7a95c8; color: #14141b;"
        >
            Send reset link
        </button>
    </form>

    <a href="{{ route('login') }}" class="text-[13px] transition-opacity hover:opacity-70" style="color: #a8a39c;">
        ← Back to sign in
    </a>

</div>
@endsection
