@extends('layouts.app')

@section('title', 'Admin Dashboard — Oreo Bites')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center">
                <x-icon name="admin" class="w-5 h-5" />
            </div>
            <div>
                <h1 class="font-display font-extrabold text-2xl text-oreo-noir">Admin Dashboard</h1>
                <p class="text-xs text-stone-500 mt-0.5">Manage products and monitor orders</p>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="container-app py-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Revenue --}}
        <div class="rounded-2xl bg-gradient-to-br from-chocolate to-cookie-brown text-milk-cream p-6 shadow-card relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-golden/15 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider font-bold text-golden">Total Revenue</p>
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <x-icon name="credit-card" class="w-4 h-4 text-golden" />
                    </div>
                </div>
                <p class="font-display font-extrabold text-4xl mt-3">₱{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-milk-cream/70 mt-1.5">From {{ $paidOrders }} paid order{{ $paidOrders !== 1 ? 's' : '' }}</p>
            </div>
        </div>

        {{-- Orders --}}
        <div class="rounded-2xl bg-oreo-noir text-milk-cream p-6 shadow-card relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-golden/15 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider font-bold text-golden">Total Orders</p>
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <x-icon name="orders" class="w-4 h-4 text-golden" />
                    </div>
                </div>
                <p class="font-display font-extrabold text-4xl mt-3">{{ $totalOrders }}</p>
                <p class="text-xs text-milk-cream/70 mt-1.5">All time</p>
            </div>
        </div>

        {{-- Products --}}
        <div class="rounded-2xl bg-white border border-stone-200 p-6 shadow-soft">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wider font-bold text-cookie-brown">Products</p>
                <div class="w-8 h-8 rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                    <x-icon name="cookie" class="w-4 h-4" />
                </div>
            </div>
            <p class="font-display font-extrabold text-4xl mt-3 text-oreo-noir">{{ count($products) }}</p>
            <p class="text-xs text-stone-500 mt-1.5">
                {{ collect($products)->where('is_active', true)->count() }} active · {{ collect($products)->where('is_active', false)->count() }} hidden
            </p>
        </div>
    </div>
</section>

{{-- Tabs --}}
<section class="container-app pb-16">
    <div class="card overflow-hidden">

        {{-- Tab nav --}}
        <div class="border-b border-stone-200 px-2 flex gap-1 bg-milk-cream/30">
            <button type="button" onclick="switchTab('products')" id="tabProducts"
                    class="tab-btn inline-flex items-center gap-2 py-3.5 px-4 text-sm font-medium border-b-2 border-chocolate text-chocolate transition-colors">
                <x-icon name="cookie" class="w-4 h-4" />
                Products
            </button>
            <button type="button" onclick="switchTab('orders')" id="tabOrders"
                    class="tab-btn inline-flex items-center gap-2 py-3.5 px-4 text-sm font-medium border-b-2 border-transparent text-stone-500 hover:text-cookie-brown transition-colors">
                <x-icon name="orders" class="w-4 h-4" />
                Orders
                <span class="badge badge-neutral">{{ count($orders) }}</span>
            </button>
        </div>

        {{-- Products Panel --}}
        <div id="panelProducts" class="p-5 md:p-6">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px]">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-200">
                            <th class="pb-3 pr-4">Product</th>
                            <th class="pb-3 pr-4">Variant</th>
                            <th class="pb-3 pr-4">Price</th>
                            <th class="pb-3 pr-4">Stock</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($products as $product)
                            <tr class="text-sm hover:bg-milk-cream/30 transition-colors">
                                <td class="py-4 pr-4">
                                    <div class="flex items-center gap-3">
                                        @if(!empty($product['image_url']))
                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-milk-cream shrink-0">
                                                <img src="{{ asset($product['image_url']) }}" alt="" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-milk-cream flex items-center justify-center text-cookie-brown shrink-0">
                                                <x-icon name="cookie" class="w-4 h-4" />
                                            </div>
                                        @endif
                                        <span class="font-medium text-oreo-noir">{{ $product['name'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="badge badge-neutral">{{ $product['variant'] }}</span>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="font-display font-bold text-oreo-noir">₱{{ $product['price'] }}</span>
                                </td>
                                <td class="py-4 pr-4">
                                    <div class="inline-flex items-center gap-2">
                                        <input type="number" min="0"
                                               value="{{ $product['stock'] }}"
                                               onchange="updateStock('{{ $product['id'] }}', this.value)"
                                               class="w-20 bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-sm
                                                      focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
                                        @if($product['stock'] <= 5 && $product['stock'] > 0)
                                            <span class="text-xs text-amber-600 font-medium">Low</span>
                                        @elseif($product['stock'] == 0)
                                            <span class="text-xs text-red-600 font-medium">Out</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 pr-4">
                                    @if($product['is_active'])
                                        <span class="badge badge-success">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="badge badge-neutral">
                                            <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>
                                            Hidden
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 text-right">
                                    <button type="button"
                                            onclick="toggleProduct('{{ $product['id'] }}', {{ $product['is_active'] ? 'false' : 'true' }})"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors
                                                   {{ $product['is_active']
                                                       ? 'text-red-600 hover:bg-red-50'
                                                       : 'text-green-600 hover:bg-green-50' }}">
                                        @if($product['is_active'])
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                            </svg>
                                            Hide
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                            Show
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Orders Panel --}}
        <div id="panelOrders" class="p-5 md:p-6 hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-200">
                            <th class="pb-3 pr-4">Order #</th>
                            <th class="pb-3 pr-4">Customer</th>
                            <th class="pb-3 pr-4">Items</th>
                            <th class="pb-3 pr-4">Total</th>
                            <th class="pb-3 pr-4">Payment</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($orders as $order)
                            @php
                                $payBadge = match($order['payment_status']) {
                                    'paid' => 'badge-success',
                                    'failed' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <tr class="text-sm hover:bg-milk-cream/30 transition-colors">
                                <td class="py-3 pr-4">
                                    <a href="/track?order={{ urlencode($order['order_number']) }}"
                                       class="font-mono text-xs text-chocolate hover:underline font-medium">
                                        {{ $order['order_number'] }}
                                    </a>
                                </td>
                                <td class="py-3 pr-4">
                                    <p class="font-medium text-oreo-noir">{{ $order['customer_name'] }}</p>
                                    <p class="text-xs text-stone-500">{{ $order['customer_phone'] }}</p>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="text-xs text-stone-600">
                                        {{ collect($order['items'])->sum('quantity') }} item{{ collect($order['items'])->sum('quantity') !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="font-display font-bold text-oreo-noir">₱{{ $order['total'] }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="badge {{ $payBadge }}">
                                        {{ strtoupper($order['payment_status']) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="badge badge-neutral">
                                        {{ str_replace('_', ' ', $order['status']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right text-xs text-stone-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($order['created_at'])->format('M d, g:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16">
                                    <div class="w-14 h-14 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                                        <x-icon name="orders" class="w-6 h-6" />
                                    </div>
                                    <p class="font-display font-bold text-oreo-noir mt-3">No orders yet</p>
                                    <p class="text-xs text-stone-500 mt-1">Orders will appear here as customers check out.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function switchTab(tab) {
    const tabs = ['products', 'orders'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1));
        const panel = document.getElementById('panel' + t.charAt(0).toUpperCase() + t.slice(1));
        if (t === tab) {
            btn.classList.add('border-chocolate', 'text-chocolate');
            btn.classList.remove('border-transparent', 'text-stone-500');
            panel.classList.remove('hidden');
        } else {
            btn.classList.remove('border-chocolate', 'text-chocolate');
            btn.classList.add('border-transparent', 'text-stone-500');
            panel.classList.add('hidden');
        }
    });
}

async function updateStock(productId, stock) {
    try {
        const response = await fetch('/admin/update-stock', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ product_id: productId, stock: parseInt(stock) }),
        });
        const data = await response.json();
        if (!data.success) alert(data.message);
    } catch (err) {
        alert('Failed to update stock.');
    }
}

async function toggleProduct(productId, isActive) {
    try {
        const response = await fetch('/admin/toggle-product', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ product_id: productId, is_active: isActive }),
        });
        const data = await response.json();
        if (data.success) location.reload();
        else alert(data.message);
    } catch (err) {
        alert('Failed to update product.');
    }
}
</script>
@endpush