@extends('layouts.app')

@section('title', 'Kitchen Display — Oreo Bites')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center">
                        <x-icon name="staff" class="w-5 h-5" />
                    </div>
                    <div>
                        <h1 class="font-display font-extrabold text-2xl text-oreo-noir">Kitchen Display</h1>
                        <p class="flex items-center gap-2 text-xs text-stone-500 mt-0.5">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Live
                            </span>
                            <span>•</span>
                            <span>Auto-refreshes every 5s</span>
                        </p>
                    </div>
                </div>
            </div>

            <button type="button" onclick="refreshQueue(true)" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="container-app py-6">
    <div class="grid grid-cols-3 gap-3 md:gap-4">
        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-700">
                    <x-icon name="orders" class="w-4 h-4" />
                </div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-amber-700 hidden sm:block">New</span>
            </div>
            <p class="font-display font-extrabold text-3xl md:text-4xl text-amber-700 mt-3" id="statPaid">0</p>
            <p class="text-xs text-amber-700 font-medium">Paid — Not Started</p>
        </div>

        <div class="rounded-2xl bg-blue-50 border border-blue-200 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700">
                    <x-icon name="staff" class="w-4 h-4" />
                </div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-blue-700 hidden sm:block">In Progress</span>
            </div>
            <p class="font-display font-extrabold text-3xl md:text-4xl text-blue-700 mt-3" id="statPreparing">0</p>
            <p class="text-xs text-blue-700 font-medium">Preparing</p>
        </div>

        <div class="rounded-2xl bg-green-50 border border-green-200 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-700">
                    <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                </div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-green-700 hidden sm:block">Waiting</span>
            </div>
            <p class="font-display font-extrabold text-3xl md:text-4xl text-green-700 mt-3" id="statReady">0</p>
            <p class="text-xs text-green-700 font-medium">Ready for Pickup</p>
        </div>
    </div>
</section>

{{-- Order queue --}}
<section class="container-app pb-16">
    <div id="orderQueue" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @include('dashboard.partials.order-queue', ['orders' => $orders])
    </div>
</section>

{{-- New order toast --}}
<div id="newOrderToast" class="fixed top-6 right-6 z-50 hidden">
    <div class="bg-oreo-noir text-milk-cream rounded-2xl shadow-lift px-5 py-4 flex items-center gap-3 min-w-[280px]">
        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-display font-bold text-sm">New Order!</p>
            <p class="text-xs text-stone-400" id="toastMessage">A new paid order just arrived</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
let previousCount = {{ collect($orders)->count() }};
let isFirstLoad = true;

async function updateStatus(orderId, status) {
    if (status === 'cancelled' && !confirm('Cancel this order?')) return;

    try {
        const response = await fetch('/staff/update-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ order_id: orderId, status }),
        });
        const data = await response.json();
        if (data.success) {
            refreshQueue();
        } else {
            alert(data.message || 'Update failed.');
        }
    } catch (err) {
        console.error(err);
        alert('Something went wrong.');
    }
}

async function refreshQueue(showFeedback = false) {
    const btn = event?.target?.closest('button');
    const original = btn?.innerHTML;

    try {
        const response = await fetch('/staff/queue', {
            headers: { 'Accept': 'text/html' }
        });
        const html = await response.text();

        document.getElementById('orderQueue').innerHTML = html;

        // Update stats
        const counts = { paid: 0, preparing: 0, ready: 0 };
        document.querySelectorAll('.order-card').forEach(card => {
            const s = card.dataset.status;
            if (counts[s] !== undefined) counts[s]++;
        });
        document.getElementById('statPaid').textContent = counts.paid;
        document.getElementById('statPreparing').textContent = counts.preparing;
        document.getElementById('statReady').textContent = counts.ready;

        const newCount = counts.paid + counts.preparing + counts.ready;

        if (!isFirstLoad && newCount > previousCount) {
            playAlertSound();
            showToast(`You have ${newCount - previousCount} new order(s)!`);
        }
        previousCount = newCount;

        if (isFirstLoad) isFirstLoad = false;

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