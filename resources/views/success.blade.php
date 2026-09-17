@extends('layouts.app')

@section('title', 'Order Confirmed — Oreo Bites')

@section('content')
<section class="container-app py-14 md:py-20">
    <div class="max-w-2xl mx-auto">

        {{-- Success icon --}}
        <div class="text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-green-100 flex items-center justify-center">
                <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white">
                    <x-icon name="check" class="w-6 h-6" stroke-width="3" />
                </div>
            </div>

            <h1 class="font-display font-extrabold text-3xl md:text-4xl text-oreo-noir mt-6">
                Order Confirmed
            </h1>
            <p class="text-stone-500 mt-2">We've received your payment. Your order is being prepared.</p>
        </div>

        {{-- Order number --}}
        <div class="mt-8 card p-6 text-center">
            <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold">Order Number</p>
            <p id="orderNumber" class="font-mono font-bold text-2xl text-chocolate mt-2">#...</p>
            <p class="text-xs text-stone-500 mt-2">Save this — you'll need it at pickup</p>
        </div>

        {{-- Order details --}}
        <div id="orderDetails" class="mt-6 card p-6 hidden">
            <h3 class="font-display font-bold text-oreo-noir mb-4">Order Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-stone-500">Customer</span>
                    <span id="detailName" class="font-medium text-oreo-noir"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Total Paid</span>
                    <span id="detailTotal" class="font-display font-bold text-chocolate"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Estimated Time</span>
                    <span id="detailEta" class="font-medium text-green-700"></span>
                </div>
            </div>
        </div>

        {{-- Pickup instructions --}}
        <div class="mt-6 rounded-2xl bg-milk-cream border border-stone-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-full bg-white border border-stone-200 flex items-center justify-center text-cookie-brown">
                    <x-icon name="location" class="w-4 h-4" />
                </div>
                <h3 class="font-display font-bold text-oreo-noir">Pickup Instructions</h3>
            </div>
            <ul class="space-y-3 text-sm text-cookie-brown">
                <li class="flex items-start gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-1.5 shrink-0"></span>
                    <span>Pickup location: <strong class="text-oreo-noir">UCC Congressional Campus Kiosk</strong></span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-1.5 shrink-0"></span>
                    <span>Present your order number at the kiosk</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-1.5 shrink-0"></span>
                    <span>Orders are kept frozen with ice packs</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mt-1.5 shrink-0"></span>
                    <span>Payment confirmed via GCash</span>
                </li>
            </ul>
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/track?order=" id="trackOrderLink" class="btn-secondary">
                <x-icon name="track" class="w-4 h-4" />
                Track This Order
            </a>
            <a href="/menu" class="btn-primary">
                Order More
                <x-icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);
    const orderNumber = params.get('order');

    const orderNumberEl = document.getElementById('orderNumber');
    const trackLink = document.getElementById('trackOrderLink');

    if (!orderNumber) {
        orderNumberEl.textContent = 'Processing...';
        return;
    }

    orderNumberEl.textContent = '#' + orderNumber;
    if (trackLink) trackLink.href = '/track?order=' + encodeURIComponent(orderNumber);

    try {
        const response = await fetch(`/api/order/${orderNumber}`);
        const data = await response.json();

        if (data && data.order_number) {
            document.getElementById('detailName').textContent = data.customer_name || '—';
            document.getElementById('detailTotal').textContent = '₱' + data.total;
            document.getElementById('detailEta').textContent = data.eta || 'N/A';
            document.getElementById('orderDetails').classList.remove('hidden');
        }
    } catch (err) {
        console.error('Failed to load order details:', err);
    }
});
</script>
@endpush