@extends('layouts.app')

@section('title', 'Set New Password — Oreo Bites')

@section('content')
<section class="container-app py-12 md:py-20">
    <div class="max-w-md mx-auto">

        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center shadow-lift">
                <x-icon name="admin" class="w-8 h-8" stroke-width="1.5" />
            </div>
            <h1 class="font-display font-extrabold text-3xl text-oreo-noir mt-4">
                Set a new password
            </h1>
            <p class="text-stone-500 text-sm mt-1">
                Must be at least 8 characters with uppercase, lowercase, number, and symbol.
            </p>
        </div>

        <div class="card p-6 md:p-8">
            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/forgot-password/reset" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="input-label">New Password</label>
                    <input id="password" type="password" name="password" required
                           autocomplete="new-password"
                           placeholder="At least 8 characters"
                           class="input-field">
                </div>

                <div>
                    <label for="password_confirmation" class="input-label">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           autocomplete="new-password"
                           placeholder="Re-enter your password"
                           class="input-field">
                </div>

                <button type="submit" class="btn-primary w-full !py-3.5">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</section>
@endsection