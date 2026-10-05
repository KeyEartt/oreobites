@extends('layouts.app')

@section('title', 'Verify Code — Oreo Bites')

@section('content')
<section class="container-app py-12 md:py-20">
    <div class="max-w-md mx-auto">

        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center shadow-lift">
                <x-icon name="check" class="w-8 h-8" stroke-width="2" />
            </div>
            <h1 class="font-display font-extrabold text-3xl text-oreo-noir mt-4">
                Check your email
            </h1>
            <p class="text-stone-500 text-sm mt-1">
                We sent a 6-digit code to <strong class="text-cookie-brown">{{ $email }}</strong>
            </p>
        </div>

        <div class="card p-6 md:p-8">
            @if(session('status'))
                <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/forgot-password/verify" class="space-y-4">
                @csrf

                <div>
                    <label for="otp" class="input-label text-center block">Enter 6-digit code</label>
                    <input id="otp" type="text" name="otp" required autofocus
                           inputmode="numeric"
                           pattern="[0-9]{6}"
                           maxlength="6"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           class="input-field text-center text-2xl tracking-[0.5em] font-mono">
                </div>

                <button type="submit" class="btn-primary w-full !py-3.5">
                    Verify Code
                </button>
            </form>

            <div class="mt-6 text-center space-y-2">
                <p class="text-xs text-stone-500">
                    Didn't receive it? Check your spam folder, or
                </p>
                <form method="POST" action="/forgot-password" class="inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="text-sm text-cookie-brown hover:text-oreo-noir font-medium">
                        request a new code
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection