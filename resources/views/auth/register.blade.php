@extends('layouts.app')

@section('title', 'Create Account — Oreo Bites')

@section('content')
<section class="container-app py-12 md:py-20">
    <div class="max-w-md mx-auto">

        {{-- Brand mark --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center shadow-lift">
                <x-icon name="cookie" class="w-8 h-8" stroke-width="1.5" />
            </div>
            <h1 class="font-display font-extrabold text-3xl text-oreo-noir mt-4">
                Create your account
            </h1>
            <p class="text-stone-500 text-sm mt-1">Save your details for faster checkout</p>
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
                        @if($errors->count() > 1)
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $errors->first() }}
                        @endif
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="/register" class="space-y-4" id="registerForm">
                @csrf

                <div>
                    <label for="full_name" class="input-label">Full Name</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                        </div>
                        <input id="full_name" type="text" name="full_name" required autofocus
                               value="{{ old('full_name') }}"
                               autocomplete="name"
                               placeholder="Juan Dela Cruz"
                               class="input-field !pl-11">
                    </div>
                </div>

                <div>
                    <label for="email" class="input-label">Email</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" required
                               value="{{ old('email') }}"
                               autocomplete="email"
                               placeholder="you@example.com"
                               class="input-field !pl-11">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="input-label">Password</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required minlength="8"
                               autocomplete="new-password"
                               placeholder="At least 8 characters"
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

                    {{-- Strength meter --}}
                    <div id="strengthMeter" class="mt-2 hidden">
                        <div class="flex gap-1 mb-1.5">
                            <div class="strength-bar h-1 flex-1 rounded-full bg-stone-200 transition-colors"></div>
                            <div class="strength-bar h-1 flex-1 rounded-full bg-stone-200 transition-colors"></div>
                            <div class="strength-bar h-1 flex-1 rounded-full bg-stone-200 transition-colors"></div>
                            <div class="strength-bar h-1 flex-1 rounded-full bg-stone-200 transition-colors"></div>
                        </div>
                        <p id="strengthLabel" class="text-xs text-stone-500"></p>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="input-label">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.746 3.746 0 0 1 21 12Z"/>
                            </svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               autocomplete="new-password"
                               placeholder="Re-enter your password"
                               class="input-field !pl-11">
                        <p id="matchHint" class="text-xs mt-1.5 hidden"></p>
                    </div>
                </div>

                {{-- Terms --}}
                <label class="flex items-start gap-2 cursor-pointer text-sm text-cookie-brown">
                    <input type="checkbox" name="terms" required
                           class="mt-0.5 w-4 h-4 rounded border-stone-300 text-chocolate focus:ring-chocolate">
                    <span>
                        I agree to the
                        <a href="/terms" target="_blank" class="text-chocolate hover:underline font-medium">Terms &amp; Conditions</a>
                        and
                        <a href="/privacy" target="_blank" class="text-chocolate hover:underline font-medium">Privacy Policy</a>
                    </span>
                </label>

                <button type="submit" class="btn-primary w-full !py-3.5">
                    <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                    Create Account
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-stone-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-xs uppercase tracking-wider text-stone-400 font-medium">
                        Already a member?
                    </span>
                </div>
            </div>

            <a href="/login" class="btn-secondary w-full">
                Log in instead
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== Password visibility toggle =====
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

    // ===== Password strength meter =====
    const strengthMeter = document.getElementById('strengthMeter');
    const strengthLabel = document.getElementById('strengthLabel');
    const bars = strengthMeter ? strengthMeter.querySelectorAll('.strength-bar') : [];

    function scorePassword(pw) {
        if (!pw) return 0;
        let score = 0;
        if (pw.length >= 8) score++;
        if (pw.length >= 12) score++;
        if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
        if (/\d/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return Math.min(score, 4);
    }

    const colors = ['bg-red-500', 'bg-amber-500', 'bg-yellow-500', 'bg-lime-500', 'bg-green-500'];
    const labels = ['Too weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const labelColors = ['text-red-600', 'text-amber-600', 'text-yellow-600', 'text-lime-600', 'text-green-600'];

    function updateStrength() {
        const pw = input.value;
        if (!pw) {
            strengthMeter.classList.add('hidden');
            return;
        }
        strengthMeter.classList.remove('hidden');

        const score = scorePassword(pw);
        const activeBars = Math.max(1, score);

        bars.forEach((bar, i) => {
            bar.className = 'strength-bar h-1 flex-1 rounded-full transition-colors ' +
                (i < activeBars ? colors[activeBars - 1] : 'bg-stone-200');
        });

        strengthLabel.textContent = labels[activeBars - 1];
        strengthLabel.className = 'text-xs ' + labelColors[activeBars - 1];
    }

    if (input && strengthMeter) {
        input.addEventListener('input', updateStrength);
    }

    // ===== Confirm password matching =====
    const confirm = document.getElementById('password_confirmation');
    const matchHint = document.getElementById('matchHint');

    function checkMatch() {
        const pw = input.value;
        const confirmPw = confirm.value;

        if (!confirmPw) {
            matchHint.classList.add('hidden');
            return;
        }

        matchHint.classList.remove('hidden');

        if (pw === confirmPw) {
            matchHint.textContent = '✓ Passwords match';
            matchHint.className = 'text-xs mt-1.5 text-green-600';
        } else {
            matchHint.textContent = '✗ Passwords do not match';
            matchHint.className = 'text-xs mt-1.5 text-red-600';
        }
    }

    if (confirm && input) {
        confirm.addEventListener('input', checkMatch);
        input.addEventListener('input', checkMatch);
    }
});
</script>
@endpush