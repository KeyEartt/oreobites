@extends('layouts.app')

@section('title', 'Checkout — Oreo Bites')

@section('content')

@php
    $authUser = session('auth_user');
@endphp

<section class="bg-white border-b border-stone-200">
    <div class="container-app py-6 md:py-8">
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-2">
            <a href="/" class="hover:text-cookie-brown">Home</a>
            <span>/</span>
            <a href="/menu" class="hover:text-cookie-brown">Menu</a>
            <span>/</span>
            <span class="text-cookie-brown font-medium">Checkout</span>
        </nav>
        <h1 class="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl text-oreo-noir">
            Checkout
        </h1>
        <p class="text-stone-500 mt-1 text-sm">Almost there. Just a few details and you're set.</p>
    </div>
</section>

<section class="container-app py-6 md:py-10">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 md:gap-8">

        <div id="qr-container" class="hidden lg:col-span-5 max-w-lg mx-auto card p-8"></div>

        {{-- LEFT --}}
        <div id="checkoutFormCol" class="lg:col-span-3 space-y-5 md:space-y-6">

            {{-- Customer Information --}}
            <div class="card p-5 md:p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown shrink-0">
                        <x-icon name="login" class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-oreo-noir">Your Details</h2>
                        <p class="text-xs text-stone-500">So we know who to expect at pickup</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="customerName" class="input-label">Full Name *</label>
                        <input id="customerName" type="text" placeholder="Juan Dela Cruz"
                               value="{{ old('customer_name', $authUser['full_name'] ?? '') }}"
                               autocomplete="name" class="input-field">
                    </div>
                    <div>
                        <label for="customerPhone" class="input-label">Phone Number *</label>
                        <input id="customerPhone" type="tel" placeholder="0917 123 4567"
                               autocomplete="tel" class="input-field">
                    </div>
                    <div>
                        <label for="customerEmail" class="input-label">Email *</label>
                        <input id="customerEmail" type="email" placeholder="juan@example.com"
                               value="{{ old('customer_email', $authUser['email'] ?? '') }}"
                               autocomplete="email" class="input-field">
                    </div>
                </div>
            </div>

            {{-- Delivery --}}
            <div class="card p-5 md:p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown shrink-0">
                        <x-icon name="location" class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-oreo-noir">Delivery</h2>
                        <p class="text-xs text-stone-500">How would you like to get your order?</p>
                    </div>
                </div>

                <div class="space-y-2" id="deliveryZonesContainer">
                    @foreach($deliveryZones as $zone)
                        <label class="flex items-center justify-between gap-3 p-4 rounded-xl border cursor-pointer transition-all
                                      has-[:checked]:border-chocolate has-[:checked]:bg-milk-cream
                                      border-stone-200 hover:border-stone-300">
                            <div class="flex items-center gap-3 min-w-0">
                                <input type="radio" name="deliveryType" value="{{ $zone['type'] }}"
                                       data-fee="{{ $zone['fee'] }}"
                                       data-name="{{ $zone['name'] }}"
                                       data-eta="{{ $zone['estimated_minutes'] }}"
                                       class="w-4 h-4 text-chocolate focus:ring-chocolate shrink-0"
                                       {{ $zone['type'] === 'pickup' ? 'checked' : '' }}>
                                <div class="min-w-0">
                                    <p class="font-medium text-oreo-noir text-sm truncate">{{ $zone['name'] }}</p>
                                    <p class="text-xs text-stone-500 truncate">
                                        @if($zone['type'] === 'pickup')
                                            Ready for pickup
                                        @else
                                            ~{{ $zone['estimated_minutes'] }} mins
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="font-display font-semibold text-oreo-noir text-sm shrink-0">
                                {{ $zone['fee'] == 0 ? 'Free' : '₱' . $zone['fee'] }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div id="addressField" class="mt-4 hidden">
                    <label for="deliveryAddress" class="input-label">Delivery Address *</label>
                    <input id="deliveryAddress" type="text" placeholder="123 Example St., Caloocan City"
                           autocomplete="street-address" class="input-field">
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="card p-5 md:p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown shrink-0">
                        <x-icon name="credit-card" class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-oreo-noir">Payment Method</h2>
                        <p class="text-xs text-stone-500">How would you like to pay?</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="flex items-start justify-between gap-3 p-4 rounded-xl border cursor-pointer transition-all
                                  has-[:checked]:border-chocolate has-[:checked]:bg-milk-cream
                                  border-stone-200 hover:border-stone-300">
                        <div class="flex items-start gap-3 min-w-0">
                            <input type="radio" name="paymentMethod" value="online" checked
                                   class="mt-0.5 w-4 h-4 text-chocolate focus:ring-chocolate shrink-0">
                            <div class="min-w-0">
                                <p class="font-medium text-oreo-noir text-sm">Pay online — GCash / Maya</p>
                                <p class="text-xs text-stone-500 mt-0.5">Scan a QR code, pay from your banking app. Order confirmed instantly.</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-start justify-between gap-3 p-4 rounded-xl border cursor-pointer transition-all
                                  has-[:checked]:border-chocolate has-[:checked]:bg-milk-cream
                                  border-stone-200 hover:border-stone-300">
                        <div class="flex items-start gap-3 min-w-0">
                            <input type="radio" name="paymentMethod" value="in_person"
                                   class="mt-0.5 w-4 h-4 text-chocolate focus:ring-chocolate shrink-0">
                            <div class="min-w-0">
                                <p class="font-medium text-oreo-noir text-sm">Pay in person — cash at pickup</p>
                                <p class="text-xs text-stone-500 mt-0.5">Reserve now, show your order number and pay when you pick up. Confirmed by staff.</p>
                            </div>
                        </div>
                    </label>
                </div>

                <div id="inPersonNote" class="hidden mt-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                    <strong class="font-semibold">Heads up:</strong> your order will be reserved for 24 hours. Bring exact cash if possible — our kiosk may not have change for large bills.
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <aside id="checkoutSummaryCol" class="lg:col-span-2">
            <div class="card p-5 md:p-6 lg:sticky lg:top-24">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown shrink-0">
                        <x-icon name="cart" class="w-4 h-4" />
                    </div>
                    <h2 class="font-display font-bold text-oreo-noir">Order Summary</h2>
                </div>

                <div id="orderSummary" class="space-y-3 divide-y divide-stone-100"></div>

                <div class="mt-5 pt-5 border-t border-stone-200 space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Subtotal</span>
                        <span id="subtotalValue" class="font-medium text-oreo-noir">₱0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Delivery Fee</span>
                        <span id="deliveryFeeValue" class="font-medium text-oreo-noir">Free</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Estimated Time</span>
                        <span id="etaValue" class="font-medium text-green-700 text-right">-</span>
                    </div>

                    <div class="flex justify-between items-baseline pt-3 border-t border-stone-200">
                        <span class="font-display font-bold text-oreo-noir text-lg">Total</span>
                        <span id="totalValue" class="font-display font-extrabold text-2xl text-chocolate">₱0</span>
                    </div>
                </div>

                <div id="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm hidden"></div>

                <button id="payButton" type="button" class="btn-primary w-full mt-5">
                    <x-icon name="credit-card" class="w-4 h-4" />
                    Pay with GCash
                </button>

                <p id="payHint" class="text-xs text-stone-500 text-center mt-3 leading-relaxed">
                    You'll see a QR code. Scan it with GCash or your bank app to pay.
                </p>

                <div class="mt-5 pt-5 border-t border-stone-100">
                    <a href="/menu" class="flex items-center justify-center gap-2 text-xs text-cookie-brown hover:text-oreo-noir font-medium">
                        <x-icon name="arrow-right" class="w-3 h-3 rotate-180" />
                        Back to menu
                    </a>
                </div>
            </div>
        </aside>
    </div>
</section>

@endsection

@push('scripts')
    @vite('resources/js/checkout.js')
@endpush