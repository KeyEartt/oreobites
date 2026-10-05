@extends('layouts.app')

@section('title', 'Forgot Password — Oreo Bites')

@section('content')
<section class="container-app py-12 md:py-20">
    <div class="max-w-md mx-auto">

        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center shadow-lift">
                <x-icon name="login" class="w-8 h-8" stroke-width="1.5" />
            </div>
            <h1 class="font-display font-extrabold text-3xl text-oreo-noir mt-4">
                Forgot your password?
            </h1>
            <p class="text-stone-500 text-sm mt-1">
                Enter your email and we'll send you a 6-digit reset code.
            </p>
        </div>

        <div class="card p-6 md:p-8">
            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('status'))
                <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="/forgot-password" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="input-label">Email</label>
                    <input id="email" type="email" name="email" required autofocus
                           value="{{ old('email') }}"
                           autocomplete="email"
                           placeholder="you@example.com"
                           class="input-field">
                </div>

                <button type="submit" class="btn-primary w-full !py-3.5">
                    Send Reset Code
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="/login" class="text-sm text-cookie-brown hover:text-oreo-noir font-medium">
                    Back to login
                </a>
            </div>
        </div>
    </div>
</section>
@endsection