@extends('layouts.admin')

@section('title', 'Kitchen Display — Oreo Bites')
@section('page-title', 'Kitchen Display')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <p class="flex items-center gap-2 text-xs text-stone-500">
        <span class="inline-flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            Live
        </span>
        <span class="hidden sm:inline">·</span>
        <span class="hidden sm:inline">Auto-refreshes every 5s</span>
    </p>

    <button type="button" onclick="refreshQueue(true)" class="btn-secondary self-start sm:self-auto !py-2 !px-4 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
        </svg>
        Refresh
    </button>
</div>

{{-- Stats — 4 cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-5">
    <div class="rounded-2xl bg-stone-50 border border-stone-200 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-stone-100 flex items-center justify-center text-stone-600 shrink-0">
            <x-icon name="clock" class="w-5 h-5" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-display font-extrabold text-2xl md:text-3xl text-stone-700 leading-none" id="statPending">0</p>
            <p class="text-[10px] md:text-xs text-stone-600 font-medium mt-1">Awaiting Payment</p>
        </div>
    </div>

    <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
            <x-icon name="orders" class="w-5 h-5" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-display font-extrabold text-2xl md:text-3xl text-amber-700 leading-none" id="statPaid">0</p>
            <p class="text-[10px] md:text-xs text-amber-700 font-medium mt-1">Paid — Not Started</p>
        </div>
    </div>

    <div class="rounded-2xl bg-blue-50 border border-blue-200 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 shrink-0">
            <x-icon name="staff" class="w-5 h-5" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-display font-extrabold text-2xl md:text-3xl text-blue-700 leading-none" id="statPreparing">0</p>
            <p class="text-[10px] md:text-xs text-blue-700 font-medium mt-1">Preparing</p>
        </div>
    </div>

    <div class="rounded-2xl bg-green-50 border border-green-200 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 shrink-0">
            <x-icon name="check" class="w-5 h-5" stroke-width="2.5" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-display font-extrabold text-2xl md:text-3xl text-green-700 leading-none" id="statReady">0</p>
            <p class="text-[10px] md:text-xs text-green-700 font-medium mt-1">Ready</p>
        </div>
    </div>
</div>

{{-- Awaiting Payment Section --}}
<section id="pendingSection" class="mb-6 hidden">
    <div class="flex items-center gap-3 mb-3">
        <h2 class="font-display font-bold text-base text-oreo-noir">Awaiting In-Person Payment</h2>
        <span class="badge bg-amber-100 text-amber-800" id="pendingBadge">0</span>
    </div>
    <p class="text-xs text-stone-500 mb-3">
        These orders are reserved. When the customer arrives, collect payment and click <strong>Confirm Payment Received</strong>.
    </p>
    <div id="pendingQueue" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
</section>

{{-- Active Queue --}}
<section>
    <div class="flex items-center gap-3 mb-3">
        <h2 class="font-display font-bold text-base text-oreo-noir">Active Orders</h2>
    </div>
    <div id="orderQueue" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @include('dashboard.partials.order-queue', ['orders' => $orders])
    </div>
</section>

<div id="newOrderToast" class="fixed top-4 right-4 left-4 sm:left-auto sm:top-6 sm:right-6 z-50 hidden">
    <div class="bg-oreo-noir text-milk-cream rounded-2xl shadow-lift px-5 py-4 flex items-center gap-3 sm:min-w-[280px]">
        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-display font-bold text-sm">New Order!</p>
            <p class="text-xs text-stone-400 truncate" id="toastMessage">A new order just arrived</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Initial counts from server-rendered content
let previousActive  = {{ collect($orders)->count() }};
let previousPending = {{ collect($pendingInPerson ?? [])->count() }};
let isFirstLoad = true;

// Render pending section on first load
document.addEventListener('DOMContentLoaded', () => {
    const pendingOrders = document.getElementById('pendingQueue');
    @if(isset($pendingInPerson) && count($pendingInPerson) > 0)
        pendingOrders.innerHTML = `@include('dashboard.partials.order-queue', ['orders' => $pendingInPerson])`;
        document.getElementById('pendingSection').classList.remove('hidden');
        document.getElementById('pendingBadge').textContent = {{ count($pendingInPerson) }};
        document.getElementById('statPending').textContent = {{ count($pendingInPerson) }};
    @endif
});

async function updateStatus(orderId, status) {
    if (status === 'cancelled' && !confirm('Cancel this order?')) return;

    try {
        const response = await fetch('/staff/update-order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ order_id: orderId, status }),
        });
        const data = await response.json();
        if (data.success) refreshQueue();
        else alert(data.message || 'Update failed.');
    } catch (err) {
        console.error(err);
        alert('Something went wrong.');
    }
}

async function markPaid(orderId) {
    if (!confirm('Confirm payment was received? This will move the order into the kitchen queue.')) return;

    try {
        const response = await fetch('/staff/mark-paid', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ order_id: orderId }),
        });
        const data = await response.json();
        if (data.success) refreshQueue();
        else alert(data.message || 'Failed to confirm.');
    } catch (err) {
        alert('Something went wrong.');
    }
}

async function refreshQueue(showFeedback = false) {
    const btn = event?.target?.closest('button');
    const original = btn?.innerHTML;

    try {
        const response = await fetch('/staff/queue', { headers: { 'Accept': 'application/json' } });
        const data = await response.json();

        document.getElementById('orderQueue').innerHTML = data.active;
        document.getElementById('pendingQueue').innerHTML = data.pending;

        const pendingCount = data.counts.pending;
        const activeCount  = data.counts.paid + data.counts.preparing + data.counts.ready;

        document.getElementById('statPending').textContent   = pendingCount;
        document.getElementById('statPaid').textContent      = data.counts.paid;
        document.getElementById('statPreparing').textContent = data.counts.preparing;
        document.getElementById('statReady').textContent     = data.counts.ready;

        // Toggle pending section visibility
        const pendingSection = document.getElementById('pendingSection');
        if (pendingCount > 0) {
            pendingSection.classList.remove('hidden');
            document.getElementById('pendingBadge').textContent = pendingCount;
        } else {
            pendingSection.classList.add('hidden');
        }

        // Alerts
        if (!isFirstLoad) {
            if (activeCount > previousActive) {
                playAlertSound();
                showToast(`You have ${activeCount - previousActive} new paid order(s)!`);
            } else if (pendingCount > previousPending) {
                playAlertSound();
                showToast(`You have ${pendingCount - previousPending} new reservation(s)!`);
            }
        }
        previousActive = activeCount;
        previousPending = pendingCount;
        isFirstLoad = false;

        if (showFeedback && btn) {
            btn.innerHTML = '✓ Refreshed';
            setTimeout(() => btn.innerHTML = original, 1000);
        }
    } catch (err) {
        console.error('Refresh failed:', err);
    }
}

function showToast(message) {
    const toast = document.getElementById('newOrderToast');
    document.getElementById('toastMessage').textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 5000);
}

function playAlertSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 880;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    } catch (e) {
        console.log('Sound not available');
    }
}

setInterval(refreshQueue, 5000);
</script>
@endpush