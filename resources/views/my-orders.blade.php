@extends('layouts.app')

@section('title', 'My Orders — Oreo Bites')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-8 md:py-10">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-stone-500 mb-2">
                    <a href="/" class="hover:text-cookie-brown">Home</a>
                    <span>/</span>
                    <span class="text-cookie-brown font-medium">My Orders</span>
                </nav>
                <h1 class="font-display font-extrabold text-3xl md:text-4xl text-oreo-noir">
                    Hi, {{ explode(' ', session('auth_user')['full_name'])[0] }}
                </h1>
                <p class="text-stone-500 mt-1 text-sm">
                    {{ count($orders) }} {{ count($orders) === 1 ? 'order' : 'orders' }} in your history
                </p>
            </div>

            <a href="/menu" class="btn-primary self-start sm:self-auto">
                <x-icon name="menu-bag" class="w-4 h-4" />
                Browse Menu
            </a>
        </div>
    </div>
</section>

<section class="container-app py-10">

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-3">
            <div class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                <x-icon name="check" class="w-3 h-3" stroke-width="3" />
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if(empty($orders))
        {{-- Empty state --}}
        <div class="card text-center py-16 md:py-20 px-6">
            <div class="w-20 h-20 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                <x-icon name="orders" class="w-9 h-9" stroke-width="1.5" />
            </div>
            <h2 class="font-display font-bold text-xl text-oreo-noir mt-5">No orders yet</h2>
            <p class="text-stone-500 mt-1 text-sm max-w-sm mx-auto leading-relaxed">
                When you place an order, it'll show up here. Time to fix that empty cart.
            </p>
            <a href="/menu" class="btn-primary mt-6">
                Order Something
                <x-icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $paymentBadge = match($order['payment_status']) {
                        'paid' => 'bg-green-100 text-green-800',
                        'failed' => 'bg-red-100 text-red-700',
                        default => 'bg-amber-100 text-amber-800',
                    };
                    $statusLabel = str_replace('_', ' ', $order['status']);
                    $isActive = in_array($order['status'], ['paid', 'preparing', 'ready']);
                @endphp

                <article class="card overflow-hidden hover:shadow-card transition-shadow">

                    {{-- Order header --}}
                    <div class="p-5 md:p-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 border-b border-stone-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-mono font-bold text-chocolate">{{ $order['order_number'] }}</p>
                                @if($isActive)
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    <span class="text-xs text-green-700 font-medium">Active</span>
                                @endif
                            </div>
                            <p class="text-xs text-stone-500 mt-1">
                                Placed {{ \Carbon\Carbon::parse($order['created_at'])->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 sm:shrink-0">
                            <span class="badge {{ $paymentBadge }}">
                                {{ strtoupper($order['payment_status']) }}
                            </span>
                            <span class="badge badge-neutral">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="p-5 md:p-6">
                        <ul class="space-y-2">
                            @foreach($order['items'] as $item)
                                <li class="flex items-center justify-between text-sm">
                                    <span class="text-cookie-brown">
                                        <span class="font-semibold text-oreo-noir">{{ $item['quantity'] }}×</span>
                                        {{ $item['name'] }}
                                    </span>
                                    <span class="text-oreo-noir font-medium">₱{{ $item['price'] * $item['quantity'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 md:px-6 py-4 bg-milk-cream/40 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-stone-500">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="location" class="w-3.5 h-3.5" />
                                {{ $order['delivery_type'] === 'pickup' ? 'Campus Pickup' : ucfirst(str_replace('_', ' ', $order['delivery_type'])) }}
                            </span>
                            @if($order['eta'])
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    {{ $order['eta'] }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4">
                            <div class="text-right">
                                <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold">Total</p>
                                <p class="font-display font-bold text-lg text-chocolate">₱{{ $order['total'] }}</p>
                            </div>
                            <a href="/track?order={{ urlencode($order['order_number']) }}"
                               class="btn-secondary !px-4 !py-2 text-xs">
                                <x-icon name="track" class="w-3.5 h-3.5" />
                                Track
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

@endsection