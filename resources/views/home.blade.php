@extends('layouts.app')

@section('title', 'Oreo Bites — Sweet, Creamy, Chocolate-Coated')
@section('description', 'Bite-sized Oreo cheesecake, frozen and glazed in dark or white chocolate. Order online for campus pickup at UCC Congressional.')

@section('content')

{{-- ============ HERO ============ --}}
<section class="relative bg-oreo-noir text-milk-cream overflow-hidden">
    <div class="absolute inset-0 pointer-events-none"
         style="background: radial-gradient(circle at 70% 30%, rgba(201,169,97,0.15) 0%, transparent 55%);"></div>

    <div class="container-app pt-16 pb-32 md:pt-24 md:pb-40 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            <div>
                <span class="badge bg-white/5 text-golden border border-white/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-golden animate-pulse-soft"></span>
                    Fresh batches daily
                </span>

                <h1 class="font-display font-extrabold tracking-tight mt-6 leading-[1.02] text-5xl md:text-6xl lg:text-7xl">
                    Sweet.<br>
                    Creamy.<br>
                    <span class="text-golden">Chocolate-coated.</span>
                </h1>

                <p class="text-stone-300 text-lg mt-6 max-w-md leading-relaxed">
                    Bite-sized Oreo cheesecake, frozen and glazed in rich dark or smooth white chocolate. Made for students, shared between friends.
                </p>

                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="/menu" class="btn-accent">
                        Order Now
                        <x-icon name="arrow-right" class="w-4 h-4" />
                    </a>
                    <a href="/track"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full
                              border border-stone-700 text-stone-200 hover:bg-white/5
                              font-medium transition-colors">
                        <x-icon name="track" class="w-4 h-4" />
                        Track Order
                    </a>
                </div>

                <div class="flex flex-wrap gap-x-6 gap-y-3 mt-10 text-sm text-stone-400">
                    <div class="flex items-center gap-2">
                        <x-icon name="location" class="w-4 h-4 text-golden" />
                        Campus pickup
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="credit-card" class="w-4 h-4 text-golden" />
                        GCash payment
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="w-4 h-4 text-golden" />
                        Ready in minutes
                    </div>
                </div>
            </div>

            <div class="relative hidden md:flex items-center justify-center">
                <div class="relative w-full aspect-square max-w-md">
                    <div class="absolute inset-0 bg-golden/10 rounded-full blur-3xl"></div>

                    <div class="relative w-full h-full rounded-full
                                bg-gradient-to-br from-cookie-brown via-oreo-noir to-oreo-noir
                                shadow-2xl border-[10px] border-oreo-noir
                                flex items-center justify-center overflow-hidden">

                        <svg viewBox="0 0 400 200" preserveAspectRatio="none"
                             class="absolute top-0 left-0 right-0 h-2/5 w-full">
                            <path fill="#FAF3E0" d="M0,0 L400,0 L400,70
                                C390,70 385,130 372,130 C359,130 356,80 342,80
                                C328,80 324,145 308,145 C292,145 288,90 274,90
                                C260,90 256,120 240,120 C224,120 220,75 206,75
                                C192,75 188,135 172,135 C156,135 152,85 138,85
                                C124,85 120,115 104,115 C88,115 84,70 70,70
                                C56,70 52,105 36,105 C20,105 16,60 0,60 Z"/>
                        </svg>

                        <x-icon name="cookie" class="w-40 h-40 text-golden relative z-10" stroke-width="0.75" />
                    </div>

                    <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lift px-4 py-3 border border-stone-200">
                        <p class="text-[10px] uppercase tracking-wider text-stone-500 font-semibold">Starting at</p>
                        <p class="font-display font-bold text-2xl text-oreo-noir">₱35<span class="text-sm text-stone-500 font-normal">/pc</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <svg viewBox="0 0 1440 120" preserveAspectRatio="none"
         class="absolute bottom-0 left-0 w-full h-16 md:h-24 text-milk-cream">
        <path fill="currentColor" d="M0,80 C120,60 240,100 360,88 C480,76 600,40 720,48 C840,56 960,96 1080,88 C1200,80 1320,60 1440,72 L1440,120 L0,120 Z"/>
    </svg>
</section>

{{-- ============ FEATURED PRODUCTS ============ --}}
<section class="container-app pt-12 md:pt-16 pb-16">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="section-title">Featured Bites</h2>
            <p class="section-subtitle">Our most loved flavors, ready to order</p>
        </div>
        <a href="/menu" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-cookie-brown hover:text-oreo-noir transition-colors">
            View all
            <x-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </div>

    @if(empty($featured))
        <div class="text-center py-16 card">
            <x-icon name="cookie" class="w-12 h-12 mx-auto text-stone-300" />
            <p class="text-stone-500 mt-3">No products available yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @endif

    <div class="text-center sm:hidden mt-8">
        <a href="/menu" class="btn-secondary">View all</a>
    </div>
</section>

{{-- ============ VALUE PROPS ============ --}}
<section class="container-app pb-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-6">
            <div class="w-12 h-12 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown mb-4">
                <x-icon name="clock" class="w-5 h-5" />
            </div>
            <h3 class="font-display font-bold text-oreo-noir">Made Fresh</h3>
            <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                Small batches prepared the same day. Never frozen for weeks — just kept cold till pickup.
            </p>
        </div>

        <div class="card p-6">
            <div class="w-12 h-12 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown mb-4">
                <x-icon name="credit-card" class="w-5 h-5" />
            </div>
            <h3 class="font-display font-bold text-oreo-noir">Pay with GCash</h3>
            <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                Scan, pay, done. Secure online payment through PayMongo. No cash needed at pickup.
            </p>
        </div>

        <div class="card p-6">
            <div class="w-12 h-12 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown mb-4">
                <x-icon name="location" class="w-5 h-5" />
            </div>
            <h3 class="font-display font-bold text-oreo-noir">Campus Pickup</h3>
            <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                Grab your order between classes at the UCC Congressional Campus kiosk.
            </p>
        </div>
    </div>
</section>

{{-- ============ CTA BAND ============ --}}
<section class="container-app pb-20">
    <div class="rounded-3xl bg-oreo-noir text-milk-cream p-8 md:p-12
                flex flex-col md:flex-row md:items-center gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-golden/10 blur-2xl"></div>

        <div class="flex-1 relative z-10">
            <h3 class="font-display font-bold text-2xl md:text-3xl">Pre-order now, pick up on campus.</h3>
            <p class="text-stone-400 mt-2">Fresh batches go live every morning. Order before they run out.</p>
        </div>

        <a href="/menu" class="btn-accent shrink-0 relative z-10">
            Browse the Menu
            <x-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </div>
</section>

@endsection