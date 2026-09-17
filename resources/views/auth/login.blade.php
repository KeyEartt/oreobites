@extends('layouts.app')

@section('title', 'Log In — Oreo Bites')

@section('content')
<section class="container-app py-12 md:py-20">
    <div class="max-w-md mx-auto">

        {{-- Brand mark --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center shadow-lift">
                <x-icon name="cookie" class="w-8 h-8" stroke-width="1.5" />
            </div>
            <h1 class="font-display font-extrabold text-3xl text-oreo-noir mt-4">
                Welcome back
            </h1>
            <p class="text-stone-500 text-sm mt-1">Log in to track orders and check out faster</p>
        </div>

        {{-- Card --}}
        <div class="card p-6 md:p-8">

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <div class="w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <div class="text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <div class="w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <div class="text-sm text-red-700">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="/login" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="input-label">Email</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" required autofocus
                               value="{{ old('email') }}"
                               autocomplete="email"
                               placeholder="you@example.com"
                               class="input-field !pl-11">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-baseline mb-1.5">
                        <label for="password" class="input-label !mb-0">Password</label>
                        <a href="/forgot-password" class="text-xs text-cookie-brown hover:text-oreo-noir font-medium">
                            Forgot?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="input-field !pl-11 !pr-11">
                        <button type="button" id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-oreo-noir transition-colors"
                                aria-label="Toggle password">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer text-sm text-cookie-brown">
                    <input type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-stone-300 text-chocolate focus:ring-chocolate">
                    Remember me on this device
                </label>

                <button type="submit" class="btn-primary w-full !py-3.5">
                    <x-icon name="login" class="w-4 h-4" />
                    Log In
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-stone-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-xs uppercase tracking-wider text-stone-400 font-medium">
                        New here?
                    </span>
                </div>
            </div>

            <a href="/register" class="btn-secondary w-full">
                Create an account
            </a>
        </div>

        {{-- Footer note --}}
        <p class="text-center text-xs text-stone-500 mt-6">
            Staff and admin accounts are issued by the store owner.
        </p>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const input = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    if (toggle && input) {
        toggle.addEventListener('click', () => {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            eyeOpen.classList.toggle('hidden', !showing);
            eyeClosed.classList.toggle('hidden', showing);
        });
    }
});
</script>
@endpush