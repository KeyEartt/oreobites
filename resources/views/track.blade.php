@extends('layouts.app')

@section('title', 'Track Your Order — Oreo Bites')

@section('content')

<section class="bg-white border-b border-stone-200">
    <div class="container-app py-8 md:py-14">
        <div class="max-w-2xl">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                <a href="/" class="hover:text-cookie-brown">Home</a>
                <span>/</span>
                <span class="text-cookie-brown font-medium">Track Order</span>
            </nav>
            <h1 class="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl text-oreo-noir">
                Track Your Order
            </h1>
            <p class="text-stone-500 mt-2 text-sm">Enter your order number to see the current status.</p>
        </div>
    </div>
</section>

<section class="container-app py-8 md:py-14">
    <div class="max-w-2xl mx-auto">

        <div class="card p-4 md:p-5">
            <form id="trackForm" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                        <x-icon name="orders" class="w-4 h-4" />
                    </div>
                    <input type="text" id="orderNumberInput"
                           placeholder="ORE-XXXXXXXX-XXXXXX"
                           class="input-field !pl-11 font-mono uppercase text-sm">
                </div>
                <button type="submit" class="btn-primary sm:w-auto w-full">
                    <x-icon name="track" class="w-4 h-4" />
                    Track
                </button>
            </form>
            <p class="text-xs text-stone-500 mt-3 text-center sm:text-left">
                Tip: You can find your order number in the confirmation email.
            </p>
        </div>

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
        <div class="card p-10 text-center">
            <div class="inline-block w-10 h-10 border-4 border-milk-cream border-t-chocolate rounded-full animate-spin"></div>
            <p class="text-stone-500 text-sm mt-4">Looking up your order...</p>
        </div>
    `;

    try {
        const response = await fetch('/api/track-order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
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
        { key: 'pending',    label: 'Order Placed',      desc: 'Waiting for payment confirmation' },
        { key: 'paid',       label: 'Payment Confirmed', desc: 'Payment received via GCash' },
        { key: 'preparing',  label: 'Preparing',         desc: 'Your bites are being made' },
        { key: 'ready',      label: 'Ready for Pickup',  desc: 'Waiting for you at the kiosk' },
        { key: 'picked_up',  label: 'Completed',         desc: 'Order picked up. Enjoy!' },
    ];

    const isCancelled = order.status === 'cancelled';
    let currentStep = steps.findIndex(s => s.key === order.status);
    if (currentStep === -1) currentStep = 0;

    const timeline = steps.map((s, i) => {
        const done = i <= currentStep && !isCancelled;
        const active = i === currentStep && !isCancelled;
        const isLast = i === steps.length - 1;
        return `
            <div class="flex gap-3 md:gap-4">
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center shrink-0 transition-all
                        ${done ? 'bg-chocolate text-milk-cream' : 'bg-milk-cream text-stone-400 border border-stone-200'}
                        ${active ? 'ring-4 ring-chocolate/20' : ''}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            ${done && !active ? '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>' : '<circle cx="12" cy="12" r="3" fill="currentColor" stroke="none"/>'}
                        </svg>
                    </div>
                    ${!isLast ? `<div class="w-0.5 h-10 md:h-12 my-1 ${done && i < currentStep ? 'bg-chocolate' : 'bg-stone-200'}"></div>` : ''}
                </div>
                <div class="flex-1 pb-3 md:pb-4 min-w-0">
                    <p class="font-display font-semibold text-sm md:text-base ${done ? 'text-oreo-noir' : 'text-stone-400'}">${s.label}</p>
                    <p class="text-xs text-stone-500 mt-0.5">${s.desc}</p>
                </div>
            </div>
        `;
    }).join('');

    const itemsHtml = order.items.map(item => `
        <div class="flex justify-between gap-3 py-2 text-sm">
            <span class="text-cookie-brown min-w-0">
                <span class="font-semibold text-oreo-noir">${item.quantity}×</span> ${item.name}
            </span>
            <span class="font-medium text-oreo-noir shrink-0">₱${item.price * item.quantity}</span>
        </div>
    `).join('');

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
            <div class="p-4 md:p-6 border-b border-stone-100">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold">Order Number</p>
                        <p class="font-mono font-bold text-base md:text-lg text-chocolate mt-1 break-all">${order.order_number}</p>
                    </div>
                    <span class="badge ${statusBadge} self-start">
                        ${order.status.replace('_', ' ').toUpperCase()}
                    </span>
                </div>
            </div>

            ${isCancelled ? `
                <div class="p-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center gap-3 text-red-700 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                        <span><strong>This order was cancelled.</strong> Contact support if this is a mistake.</span>
                    </div>
                </div>
            ` : `
                <div class="p-4 md:p-6">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-4">Progress</p>
                    ${timeline}
                </div>
            `}

            <div class="p-4 md:p-6 border-t border-stone-100 bg-milk-cream/30">
                <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-3">Items</p>
                <div class="divide-y divide-stone-100">
                    ${itemsHtml}
                </div>
                <div class="flex justify-between items-baseline mt-4 pt-4 border-t border-stone-200">
                    <span class="font-display font-bold text-oreo-noir">Total</span>
                    <span class="font-display font-extrabold text-xl text-chocolate">₱${order.total}</span>
                </div>
            </div>

            <div class="p-4 md:p-6 border-t border-stone-100 space-y-3 text-sm">
                <div class="flex items-center gap-3 text-cookie-brown">
                    <x-icon name="login" class="w-4 h-4 text-stone-400 shrink-0" />
                    <span class="truncate">${order.customer_name}</span>
                </div>
                <div class="flex items-start gap-3 text-cookie-brown">
                    <x-icon name="location" class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" />
                    <span>${order.delivery_type === 'pickup' ? 'Campus Pickup — UCC Congressional' : (order.delivery_address || '—')}</span>
                </div>
                <div class="flex items-center gap-3 text-cookie-brown">
                    <x-icon name="clock" class="w-4 h-4 text-stone-400 shrink-0" />
                    <span>${order.eta || 'N/A'}</span>
                </div>
            </div>
        </div>
    `;
}
</script>
@endpush