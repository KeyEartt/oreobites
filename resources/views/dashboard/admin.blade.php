@extends('layouts.app')

@section('title', 'Admin Dashboard — Oreo Bites')

@section('content')

<section class="bg-white border-b border-stone-200">
    <div class="container-app py-5 md:py-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 md:w-11 md:h-11 rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center shrink-0">
                <x-icon name="admin" class="w-5 h-5" />
            </div>
            <div>
                <h1 class="font-display font-extrabold text-xl md:text-2xl text-oreo-noir">Admin Dashboard</h1>
                <p class="text-xs text-stone-500 mt-0.5">Business health, products, and orders</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ KPI SECTION ============ --}}

<section class="container-app py-5 md:py-6">
    <h2 class="font-display font-bold text-sm uppercase tracking-wider text-cookie-brown mb-3">Revenue</h2>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="rounded-2xl bg-gradient-to-br from-chocolate to-cookie-brown text-milk-cream p-4 md:p-5 shadow-card relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-golden/15 blur-2xl"></div>
            <div class="relative">
                <p class="text-[10px] md:text-xs uppercase tracking-wider font-bold text-golden">Today</p>
                <p class="font-display font-extrabold text-2xl md:text-3xl mt-2">₱{{ number_format($revenueToday) }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-oreo-noir text-milk-cream p-4 md:p-5 shadow-card relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-golden/15 blur-2xl"></div>
            <div class="relative">
                <p class="text-[10px] md:text-xs uppercase tracking-wider font-bold text-golden">Last 7 days</p>
                <p class="font-display font-extrabold text-2xl md:text-3xl mt-2">₱{{ number_format($revenue7d) }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-stone-200 p-4 md:p-5 shadow-soft">
            <p class="text-[10px] md:text-xs uppercase tracking-wider font-bold text-cookie-brown">Last 30 days</p>
            <p class="font-display font-extrabold text-2xl md:text-3xl mt-2 text-oreo-noir">₱{{ number_format($revenue30d) }}</p>
        </div>

        <div class="rounded-2xl bg-white border border-stone-200 p-4 md:p-5 shadow-soft">
            <p class="text-[10px] md:text-xs uppercase tracking-wider font-bold text-cookie-brown">All time</p>
            <p class="font-display font-extrabold text-2xl md:text-3xl mt-2 text-oreo-noir">₱{{ number_format($revenueAll) }}</p>
            <p class="text-[11px] text-stone-500 mt-1">{{ $paidOrdersCount }} paid order{{ $paidOrdersCount !== 1 ? 's' : '' }}</p>
        </div>
    </div>
</section>

{{-- Order funnel --}}
<section class="container-app pb-5 md:pb-6">
    <div class="card p-5 md:p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-display font-bold text-oreo-noir">Order Funnel</h2>
                <p class="text-xs text-stone-500 mt-0.5">Where every order currently sits</p>
            </div>
            <span class="badge badge-neutral">{{ $totalOrders }} total</span>
        </div>

        @php
            $maxFunnel = max($funnel) ?: 1;
            $funnelStages = [
                'pending'   => ['label' => 'Pending Payment', 'color' => 'bg-amber-400'],
                'paid'      => ['label' => 'Paid',            'color' => 'bg-blue-500'],
                'preparing' => ['label' => 'Preparing',       'color' => 'bg-chocolate'],
                'ready'     => ['label' => 'Ready',           'color' => 'bg-green-500'],
                'picked_up' => ['label' => 'Picked Up',       'color' => 'bg-stone-400'],
                'cancelled' => ['label' => 'Cancelled',       'color' => 'bg-red-500'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($funnelStages as $key => $meta)
                @php
                    $count = $funnel[$key];
                    $pct   = $maxFunnel > 0 ? ($count / $maxFunnel) * 100 : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <span class="w-28 md:w-32 text-xs font-medium text-cookie-brown shrink-0">{{ $meta['label'] }}</span>
                    <div class="flex-1 bg-milk-cream rounded-full h-6 overflow-hidden min-w-0">
                        <div class="{{ $meta['color'] }} h-full rounded-full transition-all duration-500"
                             style="width: {{ $count > 0 ? max($pct, 2) : 0 }}%"></div>
                    </div>
                    <span class="w-10 text-right text-sm font-bold text-oreo-noir shrink-0">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Top products + Peak hours --}}
<section class="container-app pb-5 md:pb-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5">

        {{-- Top products --}}
        <div class="card p-5 md:p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-display font-bold text-oreo-noir">Top Products</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Revenue, last 30 days</p>
                </div>
            </div>

            @if(empty($topProducts))
                <div class="text-center py-10">
                    <div class="w-12 h-12 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                        <x-icon name="cookie" class="w-5 h-5" />
                    </div>
                    <p class="text-sm text-stone-500 mt-3">No sales data yet</p>
                </div>
            @else
                <div class="space-y-3">
                    @php $maxRevenue = max(array_column($topProducts, 'revenue')) ?: 1; @endphp
                    @foreach($topProducts as $i => $p)
                        @php $pct = ($p['revenue'] / $maxRevenue) * 100; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-medium text-oreo-noir truncate pr-2">
                                    <span class="text-xs text-stone-400 font-mono mr-1.5">#{{ $i + 1 }}</span>
                                    {{ $p['name'] }}
                                </span>
                                <span class="text-sm font-bold text-chocolate shrink-0">₱{{ number_format($p['revenue']) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-milk-cream rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-chocolate h-full rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-stone-500 shrink-0 w-16 text-right">{{ $p['qty'] }} sold</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Peak hours --}}
        <div class="card p-5 md:p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-display font-bold text-oreo-noir">Peak Hours</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Orders by hour, last 30 days (PHT)</p>
                </div>
            </div>

            @php
                $maxHour = max($hourCounts) ?: 1;
                $total30 = array_sum($hourCounts);
            @endphp

            @if($total30 === 0)
                <div class="text-center py-10">
                    <div class="w-12 h-12 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                        <x-icon name="clock" class="w-5 h-5" />
                    </div>
                    <p class="text-sm text-stone-500 mt-3">No orders in the last 30 days</p>
                </div>
            @else
                <div class="flex items-end gap-0.5 md:gap-1 h-32">
                    @for($h = 0; $h < 24; $h++)
                        @php
                            $count  = $hourCounts[$h];
                            $height = $count > 0 ? max(($count / $maxHour) * 100, 8) : 0;
                        @endphp
                        <div class="flex-1 h-full flex flex-col justify-end group relative">
                            <div class="w-full bg-chocolate/80 hover:bg-chocolate rounded-t transition-all"
                                 style="height: {{ $height }}%"
                                 title="{{ $count }} order{{ $count !== 1 ? 's' : '' }} at {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00"></div>
                        </div>
                    @endfor
                </div>
                <div class="flex justify-between text-[10px] text-stone-400 mt-2 font-mono">
                    <span>00</span>
                    <span>06</span>
                    <span>12</span>
                    <span>18</span>
                    <span>23</span>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Conversion metrics --}}
<section class="container-app pb-5 md:pb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
        <div class="card p-5 md:p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 shrink-0">
                    <x-icon name="close" class="w-5 h-5" stroke-width="2" />
                </div>
                <div>
                    <h3 class="font-display font-bold text-oreo-noir">Cancellation Rate</h3>
                    <p class="text-xs text-stone-500">Last 30 days</p>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="font-display font-extrabold text-4xl text-oreo-noir">{{ $cancellationRate }}%</p>
                <p class="text-sm text-stone-500">{{ $cancelled30d }} of {{ $orders30dCount }}</p>
            </div>
        </div>

        <div class="card p-5 md:p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                    <x-icon name="orders" class="w-5 h-5" stroke-width="2" />
                </div>
                <div>
                    <h3 class="font-display font-bold text-oreo-noir">Repeat Customers</h3>
                    <p class="text-xs text-stone-500">All time</p>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="font-display font-extrabold text-4xl text-oreo-noir">{{ $repeatRate }}%</p>
                <p class="text-sm text-stone-500">{{ $repeatEmails }} of {{ $uniqueEmails }} customers</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ MANAGE SECTION (existing tabs) ============ --}}

<section class="container-app pb-16">
    <div class="card overflow-hidden">

        {{-- Tabs --}}
        <div class="border-b border-stone-200 px-2 flex gap-1 bg-milk-cream/30 overflow-x-auto">
            <button type="button" onclick="switchTab('products')" id="tabProducts"
                    class="tab-btn inline-flex items-center gap-2 py-3.5 px-4 text-sm font-medium border-b-2 border-chocolate text-chocolate transition-colors whitespace-nowrap">
                <x-icon name="cookie" class="w-4 h-4" />
                Products
            </button>
            <button type="button" onclick="switchTab('orders')" id="tabOrders"
                    class="tab-btn inline-flex items-center gap-2 py-3.5 px-4 text-sm font-medium border-b-2 border-transparent text-stone-500 hover:text-cookie-brown transition-colors whitespace-nowrap">
                <x-icon name="orders" class="w-4 h-4" />
                Orders
                <span class="badge badge-neutral">{{ count($orders) }}</span>
            </button>
        </div>

        {{-- Products Panel --}}
        <div id="panelProducts" class="p-4 md:p-6">
            <div class="md:hidden flex items-center gap-2 mb-3 text-xs text-stone-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>
                Swipe horizontally to see more
            </div>

            <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
                <table class="w-full min-w-[680px]">
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
                                <td class="py-3.5 pr-4">
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
                                        <span class="font-medium text-oreo-noir whitespace-nowrap">{{ $product['name'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 pr-4"><span class="badge badge-neutral">{{ $product['variant'] }}</span></td>
                                <td class="py-3.5 pr-4"><span class="font-display font-bold text-oreo-noir whitespace-nowrap">₱{{ $product['price'] }}</span></td>
                                <td class="py-3.5 pr-4">
                                    <div class="inline-flex items-center gap-2">
                                        <input type="number" min="0"
                                               value="{{ $product['stock'] }}"
                                               onchange="updateStock('{{ $product['id'] }}', this.value)"
                                               class="w-20 bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
                                        @if($product['stock'] <= 5 && $product['stock'] > 0)
                                            <span class="text-xs text-amber-600 font-medium">Low</span>
                                        @elseif($product['stock'] == 0)
                                            <span class="text-xs text-red-600 font-medium">Out</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 pr-4">
                                    @if($product['is_active'])
                                        <span class="badge badge-success"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active</span>
                                    @else
                                        <span class="badge badge-neutral"><span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>Hidden</span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-right">
                                    <button type="button"
                                            onclick="toggleProduct('{{ $product['id'] }}', {{ $product['is_active'] ? 'false' : 'true' }})"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap
                                                   {{ $product['is_active'] ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}">
                                        {{ $product['is_active'] ? 'Hide' : 'Show' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Orders Panel --}}
        <div id="panelOrders" class="p-4 md:p-6 hidden">
            <div class="md:hidden flex items-center gap-2 mb-3 text-xs text-stone-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>
                Swipe horizontally to see more
            </div>

            <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
                <table class="w-full min-w-[820px]">
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
                                    'paid'   => 'badge-success',
                                    'failed' => 'badge-danger',
                                    default  => 'badge-warning',
                                };
                                $itemsQty = collect($order['items'] ?? [])->sum('quantity');
                            @endphp
                            <tr class="text-sm hover:bg-milk-cream/30 transition-colors">
                                <td class="py-3 pr-4">
                                    <a href="/track?order={{ urlencode($order['order_number']) }}"
                                       class="font-mono text-xs text-chocolate hover:underline font-medium whitespace-nowrap">
                                        {{ $order['order_number'] }}
                                    </a>
                                </td>
                                <td class="py-3 pr-4">
                                    <p class="font-medium text-oreo-noir whitespace-nowrap">{{ $order['customer_name'] }}</p>
                                    <p class="text-xs text-stone-500 whitespace-nowrap">{{ $order['customer_phone'] }}</p>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="text-xs text-stone-600 whitespace-nowrap">
                                        {{ $itemsQty }} item{{ $itemsQty !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="font-display font-bold text-oreo-noir whitespace-nowrap">₱{{ $order['total'] }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="badge {{ $payBadge }} whitespace-nowrap">{{ strtoupper($order['payment_status']) }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="badge badge-neutral whitespace-nowrap">{{ str_replace('_', ' ', $order['status']) }}</span>
                                </td>
                                <td class="py-3 text-right text-xs text-stone-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($order['created_at'])->timezone('Asia/Manila')->format('M d, g:i A') }}
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