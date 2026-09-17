@extends('layouts.app')

@section('title', 'Track Your Order — Oreo Bites')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-10 md:py-14">
        <div class="max-w-2xl">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                <a href="/" class="hover:text-cookie-brown">Home</a>
                <span>/</span>
                <span class="text-cookie-brown font-medium">Track Order</span>
            </nav>
            <h1 class="font-display font-extrabold text-3xl md:text-4xl text-oreo-noir">
                Track Your Order
            </h1>
            <p class="text-stone-500 mt-2 text-sm">Enter your order number to see the current status.</p>
        </div>
    </div>
</section>

<section class="container-app py-10 md:py-14">
    <div class="max-w-2xl mx-auto">

        {{-- Search form --}}
        <div class="card p-5">
            <form id="trackForm" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                        <x-icon name="orders" class="w-4 h-4" />
                    </div>
                    <input type="text" id="orderNumberInput"
                           placeholder="ORE-XXXXXXXX-XXXXXX"
                           class="input-field !pl-11 font-mono uppercase">
                </div>
                <button type="submit" class="btn-primary sm:w-auto w-full">
                    <x-icon name="track" class="w-4 h-4" />
                    Track
                </button>
            </form>
            <p class="text-xs text-stone-500 mt-3 text-center sm:text-left">
                Tip: You can find your order number in the confirmation email or on your receipt.
            </p>
        </div>

        {{-- Result area --}}
        <div id="result" class="mt-6"></div>
    </div>
</section>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const urlParams = new URLSearchParams(window.location.search);
const prefilled = urlParams.get('order');

document.addEventListener('DOMContentLoaded', () => {
    if (prefilled) {
        document.getElementById('orderNumberInput').value = prefilled;
        document.getElementById('trackForm').dispatchEvent(new Event('submit'));
    }
});

document.getElementById('trackForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('orderNumberInput');
    const result = document.getElementById('result');
    const orderNumber = input.value.trim().toUpperCase();

    if (!orderNumber) return;

    result.innerHTML = `
        <div class="card p-12 text-center">
            <div class="inline-block w-10 h-10 border-4 border-milk-cream border-t-chocolate rounded-full animate-spin"></div>
            <p class="text-stone-500 text-sm mt-4">Looking up your order...</p>
        </div>
    `;

    try {
        const response = await fetch('/api/track-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ order_number: orderNumber })
        });

        const data = await response.json();

        if (!response.ok) {
            result.innerHTML = `
                <div class="card p-8 text-center border-red-200 bg-red-50">
                    <div class="w-12 h-12 mx-auto rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <p class="font-display font-bold text-red-800 mt-3">Order not found</p>
                    <p class="text-sm text-red-600 mt-1">${data.error || 'Double-check your order number and try again.'}</p>
                </div>
            `;
            return;
        }

        result.innerHTML = renderOrder(data);

    } catch (err) {
        console.error(err);
        result.innerHTML = `
            <div class="card p-8 text-center border-red-200 bg-red-50">
                <p class="font-display font-bold text-red-800">Network error</p>
                <p class="text-sm text-red-600 mt-1">Please check your connection and try again.</p>
            </div>
        `;
    }
});

function renderOrder(order) {
    const steps = [
        { key: 'pending',    label: 'Order Placed',      desc: 'Waiting for payment confirmation', icon: 'orders' },
        { key: 'paid',       label: 'Payment Confirmed', desc: 'Payment received via GCash',       icon: 'credit-card' },
        { key: 'preparing',  label: 'Preparing',         desc: 'Your bites are being made',        icon: 'staff' },
        { key: 'ready',      label: 'Ready for Pickup',  desc: 'Waiting for you at the kiosk',     icon: 'check' },
        { key: 'picked_up',  label: 'Completed',         desc: 'Order picked up. Enjoy!',          icon: 'check' },
    ];

    const isCancelled = order.status === 'cancelled';
    let currentStep = steps.findIndex(s => s.key === order.status);
    if (currentStep === -1) currentStep = 0;

    // Timeline
    const timeline = steps.map((s, i) => {
        const done = i <= currentStep && !isCancelled;
        const active = i === currentStep && !isCancelled;
        const isLast = i === steps.length - 1;
        return `
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all
                        ${done ? 'bg-chocolate text-milk-cream' : 'bg-milk-cream text-stone-400 border border-stone-200'}
                        ${active ? 'ring-4 ring-chocolate/20' : ''}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                            ${done && !active ? '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>' : getIconPath(s.icon)}
                        </svg>
                    </div>
                    ${!isLast ? `<div class="w-0.5 h-12 my-1 ${done && i < currentStep ? 'bg-chocolate' : 'bg-stone-200'}"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <p class="font-display font-semibold ${done ? 'text-oreo-noir' : 'text-stone-400'}">${s.label}</p>
                    <p class="text-xs text-stone-500 mt-0.5">${s.desc}</p>
                </div>
            </div>
        `;
    }).join('');

    // Items
    const itemsHtml = order.items.map(item => `
        <div class="flex justify-between py-2 text-sm">
            <span class="text-cookie-brown">
                <span class="font-semibold text-oreo-noir">${item.quantity}×</span> ${item.name}
            </span>
            <span class="font-medium text-oreo-noir">₱${item.price * item.quantity}</span>
        </div>
    `).join('');

    // Status badge class
    const statusBadge = {
        'pending':    'bg-amber-100 text-amber-800',
        'paid':       'bg-blue-100 text-blue-800',
        'preparing':  'bg-amber-100 text-amber-800',
        'ready':      'bg-green-100 text-green-800',
        'picked_up':  'bg-stone-100 text-stone-700',
        'cancelled':  'bg-red-100 text-red-700',
    }[order.status] || 'bg-stone-100 text-stone-700';

    return `
        <div class="card overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-stone-100">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold">Order Number</p>
                        <p class="font-mono font-bold text-lg text-chocolate mt-1">${order.order_number}</p>
                    </div>
                    <span class="badge ${statusBadge}">
                        ${order.status.replace('_', ' ').toUpperCase()}
                    </span>
                </div>
            </div>

            {{-- Cancelled notice --}}
            ${isCancelled ? `
                <div class="p-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center gap-3 text-red-700 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                        <span><strong>This order was cancelled.</strong> If this is a mistake, contact support.</span>
                    </div>
                </div>
            ` : `
                <div class="p-6">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-4">Progress</p>
                    ${timeline}
                </div>
            `}

            {{-- Items --}}
            <div class="p-6 border-t border-stone-100 bg-milk-cream/30">
                <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-3">Items</p>
                <div class="divide-y divide-stone-100">
                    ${itemsHtml}
                </div>
                <div class="flex justify-between items-baseline mt-4 pt-4 border-t border-stone-200">
                    <span class="font-display font-bold text-oreo-noir">Total</span>
                    <span class="font-display font-extrabold text-xl text-chocolate">₱${order.total}</span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="p-6 border-t border-stone-100 space-y-3 text-sm">
                <div class="flex items-center gap-3 text-cookie-brown">
                    <x-icon name="login" class="w-4 h-4 text-stone-400 shrink-0" />
                    <span>${order.customer_name}</span>
                </div>
                <div class="flex items-center gap-3 text-cookie-brown">
                    <x-icon name="location" class="w-4 h-4 text-stone-400 shrink-0" />
                    <span>${order.delivery_type === 'pickup' ? 'Campus Pickup — UCC Congressional' : order.delivery_address || '—'}</span>
                </div>
                <div class="flex items-center gap-3 text-cookie-brown">
                    <x-icon name="clock" class="w-4 h-4 text-stone-400 shrink-0" />
                    <span>${order.eta || 'N/A'}</span>
                </div>
            </div>
        </div>
    `;
}

function getIconPath(name) {
    const paths = {
        'orders': '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>',
        'credit-card': '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>',
        'staff': '<path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z"/>',
        'check': '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>',
    };
    return paths[name] || paths['orders'];
}
</script>
@endpush