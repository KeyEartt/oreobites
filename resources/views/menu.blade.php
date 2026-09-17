@extends('layouts.app')

@section('title', 'Menu — Oreo Bites')
@section('description', 'Browse our full menu of Oreo Cheesecake Bites — dark, white, and box sets. Order online for campus pickup.')

@section('content')

{{-- Page header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-10 md:py-14">
        <div class="max-w-2xl">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                <a href="/" class="hover:text-cookie-brown">Home</a>
                <span>/</span>
                <span class="text-cookie-brown font-medium">Menu</span>
            </nav>
            <h1 class="font-display font-extrabold text-4xl md:text-5xl tracking-tight text-oreo-noir">
                Our Menu
            </h1>
            <p class="text-stone-500 mt-3 text-lg leading-relaxed">
                Bite-sized, frozen, chocolate-glazed. Pick your flavor — or grab a box to share.
            </p>
        </div>
    </div>
</section>

{{-- Products --}}
<section class="container-app py-12 md:py-16">
    @if(empty($products))
        <div class="card text-center py-20">
            <x-icon name="cookie" class="w-16 h-16 mx-auto text-stone-300" />
            <h2 class="font-display font-bold text-xl text-oreo-noir mt-4">No products yet</h2>
            <p class="text-stone-500 mt-1">Check back soon — new batches coming.</p>
        </div>
    @else
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-stone-500">
                Showing <span class="font-semibold text-cookie-brown">{{ count($products) }}</span>
                {{ count($products) === 1 ? 'product' : 'products' }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @endif
</section>

{{-- Box promo --}}
<section class="container-app pb-20">
    <div class="rounded-3xl bg-gradient-to-br from-oreo-noir to-cookie-brown
                text-milk-cream p-8 md:p-12
                grid md:grid-cols-2 gap-8 items-center
                relative overflow-hidden">

        <div class="absolute -left-20 -bottom-20 w-64 h-64 rounded-full bg-golden/10 blur-3xl"></div>

        <div class="relative z-10">
            <span class="badge bg-golden text-oreo-noir">
                Save ₱30
            </span>
            <h3 class="font-display font-extrabold text-3xl md:text-4xl mt-4 leading-tight">
                Get a Box.<br>
                <span class="text-golden">Share the bite.</span>
            </h3>
            <p class="text-stone-300 mt-4 leading-relaxed max-w-md">
                6 pieces — mix and match dark and white to your heart's content. Perfect for gifting, sharing, or a very serious personal commitment.
            </p>

            <div class="flex flex-wrap gap-3 mt-6">
                <a href="/menu" class="btn-accent">
                    Order a Box
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>
        </div>

        <div class="relative z-10 flex justify-center">
            <div class="text-center">
                <p class="text-xs uppercase tracking-wider text-stone-400 font-semibold">Box of 6</p>
                <p class="font-display font-extrabold text-6xl md:text-7xl text-golden mt-1">₱180</p>
                <p class="text-sm text-stone-400 line-through mt-1">₱210 separately</p>
            </div>
        </div>
    </div>
</section>

@endsection