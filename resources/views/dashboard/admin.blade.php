@extends('layouts.admin')

@section('title', 'Admin Dashboard — Oreo Bites')
@section('page-title', 'Dashboard')

@section('content')

@php $tz = 'Asia/Manila'; @endphp

{{-- ============ TAB: OVERVIEW ============ --}}
<div id="panelOverview" class="admin-panel space-y-5">

    <div>
        <h2 class="font-display font-bold text-xs uppercase tracking-wider text-cookie-brown mb-3">Revenue</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-2xl bg-gradient-to-br from-chocolate to-cookie-brown text-milk-cream p-4 shadow-card relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-golden/15 blur-2xl"></div>
                <div class="relative">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-golden">Today</p>
                    <p class="font-display font-extrabold text-xl md:text-2xl mt-1.5">₱{{ number_format($revenueToday) }}</p>
                </div>
            </div>
            <div class="rounded-2xl bg-oreo-noir text-milk-cream p-4 shadow-card relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-golden/15 blur-2xl"></div>
                <div class="relative">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-golden">7 days</p>
                    <p class="font-display font-extrabold text-xl md:text-2xl mt-1.5">₱{{ number_format($revenue7d) }}</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white border border-stone-200 p-4">
                <p class="text-[10px] uppercase tracking-wider font-bold text-cookie-brown">30 days</p>
                <p class="font-display font-extrabold text-xl md:text-2xl mt-1.5 text-oreo-noir">₱{{ number_format($revenue30d) }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-stone-200 p-4">
                <p class="text-[10px] uppercase tracking-wider font-bold text-cookie-brown">All time</p>
                <p class="font-display font-extrabold text-xl md:text-2xl mt-1.5 text-oreo-noir">₱{{ number_format($revenueAll) }}</p>
                <p class="text-[10px] text-stone-500 mt-0.5">{{ $paidOrdersCount }} paid</p>
            </div>
        </div>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-display font-bold text-oreo-noir">Order Funnel</h2>
                <p class="text-xs text-stone-500 mt-0.5">Where every order currently sits</p>
            </div>
            <span class="badge badge-neutral">{{ $totalOrders }} total</span>
        </div>

        @php
            $maxFunnel = max($funnel) ?: 1;
            $funnelStages = [
                'pending'   => ['label' => 'Pending',   'color' => 'bg-amber-400'],
                'paid'      => ['label' => 'Paid',      'color' => 'bg-blue-500'],
                'preparing' => ['label' => 'Preparing', 'color' => 'bg-chocolate'],
                'ready'     => ['label' => 'Ready',     'color' => 'bg-green-500'],
                'picked_up' => ['label' => 'Picked Up', 'color' => 'bg-stone-400'],
                'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-stone-300'],
            ];
        @endphp

        <div class="space-y-2">
            @foreach($funnelStages as $key => $meta)
                @php
                    $count = $funnel[$key];
                    $pct   = $maxFunnel > 0 ? ($count / $maxFunnel) * 100 : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <span class="w-20 md:w-24 text-xs font-medium text-cookie-brown shrink-0">{{ $meta['label'] }}</span>
                    <div class="flex-1 bg-milk-cream rounded-full h-5 overflow-hidden min-w-0">
                        <div class="{{ $meta['color'] }} h-full rounded-full transition-all duration-500"
                             style="width: {{ $count > 0 ? max($pct, 2) : 0 }}%"></div>
                    </div>
                    <span class="w-8 text-right text-sm font-bold text-oreo-noir shrink-0">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="card p-5">
            <div class="mb-4">
                <h2 class="font-display font-bold text-oreo-noir">Top Products</h2>
                <p class="text-xs text-stone-500 mt-0.5">Revenue, last 30 days</p>
            </div>
            @if(empty($topProducts))
                <div class="text-center py-8">
                    <div class="w-12 h-12 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                        <x-icon name="cookie" class="w-5 h-5" />
                    </div>
                    <p class="text-sm text-stone-500 mt-3">No sales data yet</p>
                </div>
            @else
                <div class="space-y-3">
                    @php $maxRevenue = max(array_column($topProducts, 'revenue')) ?: 1; @endphp
                    @foreach($topProducts as $p)
                        @php $pct = ($p['revenue'] / $maxRevenue) * 100; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-oreo-noir truncate pr-2">{{ $p['name'] }}</span>
                                <span class="text-sm font-bold text-chocolate shrink-0">₱{{ number_format($p['revenue']) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-milk-cream rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-chocolate h-full rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-stone-500 shrink-0 w-14 text-right">{{ $p['qty'] }} sold</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card p-5">
            <div class="mb-4">
                <h2 class="font-display font-bold text-oreo-noir">Peak Hours</h2>
                <p class="text-xs text-stone-500 mt-0.5">Orders by hour, last 30 days (PHT)</p>
            </div>
            @php
                $maxHour = max($hourCounts) ?: 1;
                $total30 = array_sum($hourCounts);
            @endphp
            @if($total30 === 0)
                <div class="text-center py-8">
                    <div class="w-12 h-12 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                        <x-icon name="clock" class="w-5 h-5" />
                    </div>
                    <p class="text-sm text-stone-500 mt-3">No orders in the last 30 days</p>
                </div>
            @else
                <div class="overflow-x-auto -mx-5 px-5">
                    <div class="flex items-end gap-1 h-28 min-w-[480px]">
                        @for($h = 0; $h < 24; $h++)
                            @php
                                $count  = $hourCounts[$h];
                                $height = $count > 0 ? max(($count / $maxHour) * 100, 8) : 0;
                            @endphp
                            <div class="flex-1 h-full flex flex-col justify-end group relative min-w-[14px]">
                                <div class="w-full bg-chocolate/80 hover:bg-chocolate rounded-t transition-all cursor-pointer"
                                     style="height: {{ $height }}%"
                                     title="{{ $count }} order{{ $count !== 1 ? 's' : '' }} at {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="flex justify-between text-[10px] text-stone-400 mt-2 font-mono">
                    <span>00</span><span>06</span><span>12</span><span>18</span><span>23</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 shrink-0">
                    <x-icon name="close" class="w-5 h-5" stroke-width="2" />
                </div>
                <div>
                    <h3 class="font-display font-bold text-oreo-noir text-sm">Cancellation Rate</h3>
                    <p class="text-xs text-stone-500">Last 30 days</p>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="font-display font-extrabold text-3xl text-oreo-noir">{{ $cancellationRate }}%</p>
                <p class="text-sm text-stone-500">{{ $cancelled30d }} of {{ $orders30dCount }}</p>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                    <x-icon name="orders" class="w-5 h-5" stroke-width="2" />
                </div>
                <div>
                    <h3 class="font-display font-bold text-oreo-noir text-sm">Repeat Customers</h3>
                    <p class="text-xs text-stone-500">All time</p>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="font-display font-extrabold text-3xl text-oreo-noir">{{ $repeatRate }}%</p>
                <p class="text-sm text-stone-500">{{ $repeatEmails }} of {{ $uniqueEmails }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ============ TAB: PRODUCTS ============ --}}
<div id="panelProducts" class="admin-panel hidden">
    <div class="card overflow-hidden">
        <div class="p-4 md:p-5 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-display font-bold text-oreo-noir">Products</h2>
                <p class="text-xs text-stone-500 mt-0.5">Manage stock and visibility</p>
            </div>
            <div class="relative w-full sm:w-64">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </div>
                <input id="productsSearch" type="search" placeholder="Search products..."
                       class="w-full bg-white border border-stone-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="text-left text-[10px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-200 bg-milk-cream/30">
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">Variant</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Stock</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTbody" class="divide-y divide-stone-100">
                    @foreach($products as $product)
                        <tr class="product-row text-sm hover:bg-milk-cream/30 transition-colors">
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3"><span class="badge badge-neutral">{{ $product['variant'] }}</span></td>
                            <td class="px-4 py-3"><span class="font-display font-bold text-oreo-noir whitespace-nowrap">₱{{ $product['price'] }}</span></td>
                            <td class="px-4 py-3">
                                <div class="inline-flex items-center gap-2">
                                    <input type="number" min="0" value="{{ $product['stock'] }}"
                                           onchange="updateStock('{{ $product['id'] }}', this.value)"
                                           class="w-20 bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
                                    @if($product['stock'] <= 5 && $product['stock'] > 0)
                                        <span class="text-xs text-amber-600 font-medium">Low</span>
                                    @elseif($product['stock'] == 0)
                                        <span class="text-xs text-red-600 font-medium">Out</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($product['is_active'])
                                    <span class="badge badge-success"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active</span>
                                @else
                                    <span class="badge badge-neutral"><span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>Hidden</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
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
        <div id="productsEmpty" class="hidden text-center py-10 border-t border-stone-100">
            <p class="text-sm text-stone-500">No products match your search.</p>
        </div>
    </div>
</div>

{{-- ============ TAB: ORDERS ============ --}}
<div id="panelOrders" class="admin-panel hidden">
    <div class="card overflow-hidden">
        <div class="p-4 md:p-5 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-display font-bold text-oreo-noir">Orders</h2>
                <p class="text-xs text-stone-500 mt-0.5">Click any row to see full detail</p>
            </div>
            <div class="relative w-full sm:w-72">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </div>
                <input id="ordersSearch" type="search" placeholder="Order #, name, phone, email..."
                       class="w-full bg-white border border-stone-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px]">
                <thead>
                    <tr class="text-left text-[10px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-200 bg-milk-cream/30">
                        <th class="px-4 py-3">Order #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Payment</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Date</th>
                    </tr>
                </thead>
                <tbody id="ordersTbody" class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                        @php
                            $payBadge = match($order['payment_status']) {
                                'paid'   => 'badge-success',
                                'failed' => 'badge-danger',
                                default  => 'badge-warning',
                            };
                            $itemsQty = collect($order['items'] ?? [])->sum('quantity');
                        @endphp
                        <tr class="order-row text-sm hover:bg-milk-cream/30 transition-colors cursor-pointer"
                            data-order="{{ json_encode([
                                'id'               => $order['id'],
                                'order_number'     => $order['order_number'],
                                'created_at'       => \Carbon\Carbon::parse($order['created_at'])->timezone($tz)->format('M d, Y · g:i A'),
                                'customer_name'    => $order['customer_name'],
                                'customer_phone'   => $order['customer_phone'],
                                'customer_email'   => $order['customer_email'],
                                'delivery_type'    => $order['delivery_type'],
                                'delivery_address' => $order['delivery_address'] ?? null,
                                'delivery_fee'     => $order['delivery_fee'] ?? 0,
                                'subtotal'         => $order['subtotal'] ?? 0,
                                'total'            => $order['total'],
                                'eta'              => $order['eta'] ?? null,
                                'status'           => $order['status'],
                                'payment_status'   => $order['payment_status'],
                                'items'            => $order['items'] ?? [],
                            ]) }}">
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-chocolate font-medium whitespace-nowrap">{{ $order['order_number'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-oreo-noir whitespace-nowrap">{{ $order['customer_name'] }}</p>
                                <p class="text-xs text-stone-500 whitespace-nowrap">{{ $order['customer_phone'] }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-stone-600 whitespace-nowrap">{{ $itemsQty }} item{{ $itemsQty !== 1 ? 's' : '' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-display font-bold text-oreo-noir whitespace-nowrap">₱{{ $order['total'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $payBadge }} whitespace-nowrap">{{ strtoupper($order['payment_status']) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge badge-neutral whitespace-nowrap">{{ str_replace('_', ' ', $order['status']) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-stone-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($order['created_at'])->timezone($tz)->format('M d, g:i A') }}
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

        <div id="ordersEmptySearch" class="hidden text-center py-10 border-t border-stone-100">
            <p class="text-sm text-stone-500">No orders match your search.</p>
        </div>

        <div class="px-4 md:px-5 py-4 border-t border-stone-100">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                <nav id="adminOrdersPaginator" aria-label="Pagination" class="flex items-center gap-1"></nav>

                <div id="adminOrdersJumpWrapper" class="hidden items-center gap-2 sm:border-l sm:border-stone-200 sm:pl-4">
                    <label for="adminOrdersJumpInput" class="text-xs text-stone-500 whitespace-nowrap">Go to</label>
                    <input id="adminOrdersJumpInput" type="number" min="1" inputmode="numeric"
                           class="w-12 bg-white border border-stone-300 rounded-md px-2 py-1 text-center text-sm focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
                    <span id="adminOrdersJumpTotal" class="text-xs text-stone-500 whitespace-nowrap"></span>
                </div>
            </div>
            <p id="adminOrdersCounter" class="text-center text-xs text-stone-500 mt-2"></p>
        </div>
    </div>
</div>

{{-- ============ ORDER DETAIL MODAL ============ --}}
<div id="orderModal" class="fixed inset-0 z-[60] hidden">
    <div id="orderModalOverlay" class="absolute inset-0 bg-oreo-noir/60 backdrop-blur-sm"></div>
    <div class="absolute inset-x-3 top-6 bottom-6 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-2xl md:max-h-[88vh] flex">
        <div class="bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden w-full max-h-full">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between gap-3 shrink-0">
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold">Order Details</p>
                    <p id="modalOrderNumber" class="font-mono font-bold text-chocolate text-base truncate"></p>
                </div>
                <button type="button" id="orderModalClose"
                        class="w-9 h-9 flex items-center justify-center rounded-full text-stone-500 hover:bg-milk-cream hover:text-oreo-noir transition-colors shrink-0"
                        aria-label="Close">
                    <x-icon name="close" class="w-5 h-5" />
                </button>
            </div>
            <div id="orderModalBody" class="flex-1 overflow-y-auto min-h-0"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ===== TAB SWITCHING =====
function switchTab(tab, pushHash = true) {
    const tabs = ['overview', 'products', 'orders'];
    if (!tabs.includes(tab)) tab = 'overview';

    tabs.forEach(t => {
        const panel = document.getElementById('panel' + t.charAt(0).toUpperCase() + t.slice(1));
        if (panel) panel.classList.toggle('hidden', t !== tab);
    });

    document.querySelectorAll('[data-tab-link]').forEach(link => {
        const isActive = link.getAttribute('data-tab-link') === tab;
        if (isActive) {
            link.classList.add('bg-chocolate', 'text-white');
            link.classList.remove('text-stone-300');
        } else {
            link.classList.remove('bg-chocolate', 'text-white');
            link.classList.add('text-stone-300');
        }
    });

    if (pushHash) history.replaceState(null, '', '#' + tab);
}

const initialTab = (location.hash || '#overview').replace('#', '');
switchTab(initialTab, false);

document.querySelectorAll('[data-tab-link]').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        switchTab(link.getAttribute('data-tab-link'));
        const sidebar = document.getElementById('adminSidebarMobile');
        const overlay = document.getElementById('adminSidebarOverlay');
        if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
});

window.addEventListener('hashchange', () => {
    const tab = (location.hash || '#overview').replace('#', '');
    switchTab(tab, false);
});

// ===== PRODUCT SEARCH =====
(function () {
    const input = document.getElementById('productsSearch');
    const tbody = document.getElementById('productsTbody');
    const empty = document.getElementById('productsEmpty');
    if (!input || !tbody) return;

    const rows = Array.from(tbody.querySelectorAll('.product-row'));

    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(row => {
            const match = q === '' || row.textContent.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        empty.classList.toggle('hidden', visible > 0);
    });
})();

// ===== PRODUCT ACTIONS =====
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

// ===== ORDERS: PAGINATION + SEARCH =====
(function () {
    const PER_PAGE = 10;
    const tbody = document.getElementById('ordersTbody');
    const searchInput = document.getElementById('ordersSearch');
    const emptySearch = document.getElementById('ordersEmptySearch');
    if (!tbody) return;

    const allRows = Array.from(tbody.querySelectorAll('.order-row'));
    if (allRows.length === 0) return;

    const paginator  = document.getElementById('adminOrdersPaginator');
    const counter    = document.getElementById('adminOrdersCounter');
    const jumpWrap   = document.getElementById('adminOrdersJumpWrapper');
    const jumpInput  = document.getElementById('adminOrdersJumpInput');
    const jumpTotal  = document.getElementById('adminOrdersJumpTotal');

    let filteredRows = allRows.slice();
    let currentPage = 1;

    function totalPages() {
        return Math.max(1, Math.ceil(filteredRows.length / PER_PAGE));
    }

    function render(page) {
        const tp = totalPages();
        currentPage = Math.max(1, Math.min(page, tp));

        allRows.forEach(r => r.style.display = 'none');
        const start = (currentPage - 1) * PER_PAGE;
        filteredRows.slice(start, start + PER_PAGE).forEach(r => r.style.display = '');

        emptySearch.classList.toggle('hidden', filteredRows.length > 0);

        jumpWrap.classList.toggle('hidden', tp <= 1);
        jumpWrap.classList.toggle('sm:flex', tp > 1);
        jumpInput.value = currentPage;
        jumpInput.max = tp;
        jumpTotal.textContent = `of ${tp}`;

        renderPaginator();
        renderCounter();
    }

    function renderPaginator() {
        paginator.innerHTML = '';
        const tp = totalPages();

        const makeBtn = (content, page, { disabled = false, active = false, icon = false } = {}) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            if (icon) btn.innerHTML = content;
            else btn.textContent = content;
            btn.className = [
                'min-w-[32px] h-8 px-2 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors',
                active   ? 'bg-oreo-noir text-white cursor-default'
                         : disabled ? 'text-stone-300 cursor-not-allowed'
                                    : 'text-cookie-brown hover:bg-milk-cream',
            ].join(' ');
            if (disabled) btn.disabled = true;
            btn.addEventListener('click', () => {
                if (disabled || active) return;
                render(page);
            });
            return btn;
        };

        const ellipsis = () => {
            const s = document.createElement('span');
            s.textContent = '…';
            s.className = 'min-w-[32px] h-8 inline-flex items-center justify-center text-stone-400 text-sm';
            return s;
        };

        // Prev
        const prevIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>';
        paginator.appendChild(makeBtn(prevIcon, currentPage - 1, { disabled: currentPage === 1, icon: true }));

        // Page numbers
        const pages = [];
        if (tp <= 8) {
            for (let i = 1; i <= tp; i++) pages.push(i);
        } else {
            if (currentPage <= 4) {
                for (let i = 1; i <= 5; i++) pages.push(i);
                pages.push('…');
                pages.push(tp);
            } else if (currentPage >= tp - 3) {
                pages.push(1);
                pages.push('…');
                for (let i = tp - 4; i <= tp; i++) pages.push(i);
            } else {
                pages.push(1);
                pages.push('…');
                for (let i = currentPage - 1; i <= currentPage + 1; i++) pages.push(i);
                pages.push('…');
                pages.push(tp);
            }
        }

        pages.forEach(p => {
            if (p === '…') paginator.appendChild(ellipsis());
            else paginator.appendChild(makeBtn(p, p, { active: p === currentPage }));
        });

        // Next
        const nextIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>';
        paginator.appendChild(makeBtn(nextIcon, currentPage + 1, { disabled: currentPage === tp, icon: true }));
    }

    function renderCounter() {
        const total = filteredRows.length;
        if (total === 0) { counter.textContent = ''; return; }
        const from = (currentPage - 1) * PER_PAGE + 1;
        const to   = Math.min(currentPage * PER_PAGE, total);
        counter.textContent = `Showing ${from}–${to} of ${total} order${total !== 1 ? 's' : ''}`;
    }

    function jumpToInputPage() {
        const target = parseInt(jumpInput.value, 10);
        if (isNaN(target)) { jumpInput.value = currentPage; return; }
        const tp = totalPages();
        render(Math.max(1, Math.min(target, tp)));
    }

    jumpInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); jumpToInputPage(); jumpInput.blur(); }
    });

    jumpInput.addEventListener('blur', () => {
        const target = parseInt(jumpInput.value, 10);
        if (!isNaN(target) && target !== currentPage) jumpToInputPage();
        else if (isNaN(target)) jumpInput.value = currentPage;
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            filteredRows = q === ''
                ? allRows.slice()
                : allRows.filter(row => row.textContent.toLowerCase().includes(q));
            render(1);
        });
    }

    render(1);

    // ===== ORDER MODAL =====
    const modal = document.getElementById('orderModal');
    const overlay = document.getElementById('orderModalOverlay');
    const closeBtn = document.getElementById('orderModalClose');
    const modalBody = document.getElementById('orderModalBody');
    const modalOrderNumber = document.getElementById('modalOrderNumber');
    if (!modal) return;

    const statusStyleMap = {
        pending:   'badge-warning',
        paid:      'badge-info',
        preparing: 'bg-chocolate/15 text-chocolate',
        ready:     'badge-success',
        picked_up: 'badge-neutral',
        cancelled: 'badge-danger',
    };
    const statusLabelMap = {
        pending:   'Awaiting Payment',
        paid:      'Paid',
        preparing: 'Preparing',
        ready:     'Ready for Pickup',
        picked_up: 'Completed',
        cancelled: 'Cancelled',
    };

    function openModal(order) {
        modalOrderNumber.textContent = order.order_number;

        const itemsHtml = (order.items || []).map(item => {
            const qty = item.quantity || 1;
            const price = item.price || 0;
            const img = item.image_url ? `/${item.image_url}` : null;
            const imgHtml = img
                ? `<img src="${img}" alt="" class="w-full h-full object-cover">`
                : `<div class="w-full h-full flex items-center justify-center text-cookie-brown/50"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="9"/></svg></div>`;
            return `
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 bg-milk-cream border border-stone-200">${imgHtml}</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-oreo-noir text-sm truncate">${item.name || 'Item'}</p>
                        <p class="text-xs text-stone-500 mt-0.5">₱${price} × ${qty}</p>
                    </div>
                    <p class="font-display font-bold text-oreo-noir text-sm shrink-0">₱${price * qty}</p>
                </div>`;
        }).join('');

        const deliveryLine = order.delivery_type === 'pickup'
            ? 'Campus Pickup — UCC Congressional'
            : `${order.delivery_type.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())}${order.delivery_address ? ' — ' + order.delivery_address : ''}`;

        let actionButtons = '';
        if (order.status === 'paid') {
            actionButtons = `<button type="button" onclick="modalUpdateStatus('${order.id}', 'preparing')" class="btn-primary !py-2.5 text-sm w-full">Start Preparing</button>`;
        } else if (order.status === 'preparing') {
            actionButtons = `<button type="button" onclick="modalUpdateStatus('${order.id}', 'ready')" class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">Mark Ready for Pickup</button>`;
        } else if (order.status === 'ready') {
            actionButtons = `<button type="button" onclick="modalUpdateStatus('${order.id}', 'picked_up')" class="w-full inline-flex items-center justify-center gap-2 bg-oreo-noir hover:bg-cookie-brown text-white font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">Picked Up</button>`;
        }

        let cancelButton = '';
        if (!['cancelled', 'picked_up'].includes(order.status)) {
            cancelButton = `<button type="button" onclick="modalUpdateStatus('${order.id}', 'cancelled')" class="w-full text-red-600 hover:bg-red-50 py-2 rounded-lg text-xs font-medium transition-colors">Cancel Order</button>`;
        }

        modalBody.innerHTML = `
            <div class="p-5 md:p-6 space-y-5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge ${statusStyleMap[order.status] || 'badge-neutral'}">${statusLabelMap[order.status] || order.status}</span>
                    <span class="badge ${order.payment_status === 'paid' ? 'badge-success' : order.payment_status === 'failed' ? 'badge-danger' : 'badge-warning'}">${order.payment_status.toUpperCase()}</span>
                    <span class="text-xs text-stone-500 ml-auto">${order.created_at}</span>
                </div>

                <div>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-2">Customer</p>
                    <p class="font-medium text-oreo-noir">${order.customer_name}</p>
                    <p class="text-sm text-stone-500">${order.customer_phone}</p>
                    <p class="text-sm text-stone-500 break-all">${order.customer_email}</p>
                </div>

                <div>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-2">Delivery</p>
                    <p class="text-sm text-cookie-brown">${deliveryLine}</p>
                    ${order.eta ? `<p class="text-xs text-stone-500 mt-1">ETA: ${order.eta}</p>` : ''}
                </div>

                <div>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-3">Items (${(order.items || []).reduce((s, i) => s + (i.quantity || 1), 0)})</p>
                    <div class="space-y-3">${itemsHtml || '<p class="text-sm text-stone-500">No items</p>'}</div>
                </div>

                <div class="bg-milk-cream/50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between text-stone-500"><span>Subtotal</span><span>₱${order.subtotal}</span></div>
                    <div class="flex justify-between text-stone-500"><span>Delivery Fee</span><span>${order.delivery_fee == 0 ? 'Free' : '₱' + order.delivery_fee}</span></div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-stone-200">
                        <span class="font-display font-bold text-oreo-noir">Total</span>
                        <span class="font-display font-extrabold text-xl text-chocolate">₱${order.total}</span>
                    </div>
                </div>

                ${(actionButtons || cancelButton) ? `<div class="space-y-2 pt-2 border-t border-stone-100">${actionButtons}${cancelButton}</div>` : ''}
            </div>
        `;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    tbody.addEventListener('click', (e) => {
        const row = e.target.closest('.order-row');
        if (!row) return;
        const raw = row.getAttribute('data-order');
        if (!raw) return;
        try { openModal(JSON.parse(raw)); }
        catch (err) { console.error('Failed to parse order data', err); }
    });

    overlay.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    window.modalUpdateStatus = async function (orderId, status) {
        if (status === 'cancelled' && !confirm('Cancel this order?')) return;
        try {
            const response = await fetch('/staff/update-order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ order_id: orderId, status }),
            });
            const data = await response.json();
            if (data.success) { closeModal(); location.reload(); }
            else alert(data.message || 'Update failed.');
        } catch (err) { alert('Something went wrong.'); }
    };
})();
</script>
@endpush