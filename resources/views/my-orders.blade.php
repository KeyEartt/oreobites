@extends('layouts.app')

@section('title', 'My Orders — Oreo Bites')

@section('content')

@php $tz = 'Asia/Manila'; @endphp

<section class="bg-white border-b border-stone-200">
    <div class="container-app py-6 md:py-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-stone-500 mb-2">
                    <a href="/" class="hover:text-cookie-brown">Home</a>
                    <span>/</span>
                    <span class="text-cookie-brown font-medium">My Orders</span>
                </nav>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl text-oreo-noir">
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

<section class="container-app py-6 md:py-8">

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-3">
            <div class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                <x-icon name="check" class="w-3 h-3" stroke-width="3" />
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if(empty($orders))
        <div class="card text-center py-16 md:py-20 px-6">
            <div class="w-20 h-20 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
                <x-icon name="orders" class="w-9 h-9" stroke-width="1.5" />
            </div>
            <h2 class="font-display font-bold text-xl text-oreo-noir mt-5">No orders yet</h2>
            <p class="text-stone-500 mt-1 text-sm max-w-sm mx-auto leading-relaxed">
                When you place an order, it'll show up here.
            </p>
            <a href="/menu" class="btn-primary mt-6">
                Order Something
                <x-icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>
    @else
        <div id="ordersList" class="space-y-3">
            @foreach($orders as $order)
                @php
                    $status    = $order['status'] ?? 'pending';
                    $payStatus = $order['payment_status'] ?? 'unpaid';
                    $isCancelled = $status === 'cancelled';
                    $isActive    = in_array($status, ['paid', 'preparing', 'ready']);

                    $statusLabel = match($status) {
                        'pending'   => 'Awaiting Payment',
                        'paid'      => 'Paid',
                        'preparing' => 'Preparing',
                        'ready'     => 'Ready for Pickup',
                        'picked_up' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default     => ucfirst($status),
                    };
                    $statusStyle = match($status) {
                        'pending'   => 'bg-amber-100 text-amber-800',
                        'paid'      => 'bg-blue-100 text-blue-800',
                        'preparing' => 'bg-chocolate/15 text-chocolate',
                        'ready'     => 'bg-green-100 text-green-800',
                        'picked_up' => 'bg-stone-100 text-stone-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default     => 'bg-stone-100 text-stone-700',
                    };

                    $progress = match($status) {
                        'pending'   => 0,
                        'paid'      => 1,
                        'preparing' => 2,
                        'ready'     => 3,
                        'picked_up' => 4,
                        default     => 0,
                    };
                    $steps = ['Placed', 'Paid', 'Preparing', 'Ready', 'Completed'];
                    $itemCount = collect($order['items'] ?? [])->sum('quantity');
                    $firstItem = $order['items'][0] ?? null;
                @endphp

                <article class="order-card card overflow-hidden" data-status="{{ $status }}">
                    <button type="button" class="order-header w-full text-left px-4 md:px-5 py-4 flex items-center gap-3 md:gap-4 hover:bg-milk-cream/30 transition-colors">
                        <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 bg-milk-cream border border-stone-200">
                            @if(!empty($firstItem['image_url']))
                                <img src="{{ asset($firstItem['image_url']) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-cookie-brown/50">
                                    <x-icon name="cookie" class="w-5 h-5" />
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-mono font-bold text-chocolate text-xs md:text-sm truncate">{{ $order['order_number'] }}</p>
                                @if($isActive)
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                                @endif
                            </div>
                            <p class="text-[11px] md:text-xs text-stone-500 mt-0.5 truncate">
                                {{ \Carbon\Carbon::parse($order['created_at'])->timezone($tz)->format('M d, Y · g:i A') }}
                                · {{ $itemCount }} item{{ $itemCount !== 1 ? 's' : '' }}
                            </p>
                        </div>

                        <div class="text-right shrink-0 hidden sm:block">
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold">Total</p>
                            <p class="font-display font-bold text-base text-chocolate">₱{{ $order['total'] }}</p>
                        </div>

                        <span class="badge {{ $statusStyle }} shrink-0 hidden md:inline-flex">{{ $statusLabel }}</span>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="order-chevron w-4 h-4 text-stone-400 shrink-0 transition-transform duration-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>

                    <div class="md:hidden px-4 pb-3 flex items-center justify-between gap-3 -mt-1">
                        <span class="badge {{ $statusStyle }}">{{ $statusLabel }}</span>
                        <span class="font-display font-bold text-sm text-chocolate">₱{{ $order['total'] }}</span>
                    </div>

                    <div class="order-body hidden border-t border-stone-100">
                        @if(!$isCancelled)
                            <div class="px-4 md:px-6 py-5 bg-milk-cream/30 border-b border-stone-100">
                                <div class="relative">
                                    <div class="absolute top-4 left-4 right-4 h-0.5 bg-stone-200"></div>
                                    <div class="absolute top-4 left-4 h-0.5 bg-chocolate transition-all duration-500"
                                         style="width: calc((100% - 2rem) * {{ $progress }} / 4)"></div>
                                    <div class="relative flex justify-between">
                                        @foreach($steps as $i => $label)
                                            @php $done = $i <= $progress; @endphp
                                            <div class="flex flex-col items-center gap-1.5 min-w-0">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                                            {{ $done ? 'bg-chocolate text-milk-cream' : 'bg-white border-2 border-stone-200 text-stone-400' }}">
                                                    @if($done && $i < $progress)
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                                        </svg>
                                                    @else
                                                        {{ $i + 1 }}
                                                    @endif
                                                </div>
                                                <span class="text-[10px] sm:text-xs font-medium text-center leading-tight {{ $done ? 'text-oreo-noir' : 'text-stone-400' }}">
                                                    {{ $label }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-red-50 border-b border-red-100">
                                <div class="flex items-center gap-3 text-red-700 text-sm">
                                    <x-icon name="close" class="w-5 h-5 shrink-0" stroke-width="2" />
                                    <span><strong>This order was cancelled.</strong> Contact support if this is a mistake.</span>
                                </div>
                            </div>
                        @endif

                        <div class="p-4 md:p-6 border-b border-stone-100">
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-3">Order Items</p>
                            <div class="space-y-3">
                                @foreach($order['items'] ?? [] as $item)
                                    @php
                                        $qty = $item['quantity'] ?? 1;
                                        $price = $item['price'] ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 bg-milk-cream border border-stone-200">
                                            @if(!empty($item['image_url']))
                                                <img src="{{ asset($item['image_url']) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-cookie-brown/50">
                                                    <x-icon name="cookie" class="w-5 h-5" />
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-oreo-noir text-sm truncate">{{ $item['name'] ?? 'Item' }}</p>
                                            <p class="text-xs text-stone-500 mt-0.5">
                                                @if(!empty($item['variant'])){{ $item['variant'] }} · @endif
                                                ₱{{ $price }} × {{ $qty }}
                                            </p>
                                        </div>
                                        <p class="font-display font-bold text-oreo-noir text-sm shrink-0">₱{{ $price * $qty }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 md:p-6 border-b border-stone-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-2">Delivery</p>
                                <div class="flex items-start gap-2 text-cookie-brown">
                                    <x-icon name="location" class="w-4 h-4 text-chocolate shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-medium text-oreo-noir">
                                            {{ $order['delivery_type'] === 'pickup' ? 'Campus Pickup' : ucfirst(str_replace('_', ' ', $order['delivery_type'])) }}
                                        </p>
                                        @if($order['delivery_type'] === 'pickup')
                                            <p class="text-xs text-stone-500 mt-0.5">UCC Congressional Campus Kiosk</p>
                                        @elseif(!empty($order['delivery_address']))
                                            <p class="text-xs text-stone-500 mt-0.5">{{ $order['delivery_address'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-2">Contact</p>
                                <div class="flex items-start gap-2 text-cookie-brown">
                                    <x-icon name="login" class="w-4 h-4 text-chocolate shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-medium text-oreo-noir">{{ $order['customer_name'] }}</p>
                                        <p class="text-xs text-stone-500 mt-0.5">{{ $order['customer_phone'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($order['eta']))
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-2">Estimated Time</p>
                                    <div class="flex items-center gap-2 text-cookie-brown">
                                        <x-icon name="clock" class="w-4 h-4 text-chocolate shrink-0" />
                                        <p class="font-medium text-oreo-noir">{{ $order['eta'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-4 md:p-6 bg-milk-cream/40 border-b border-stone-100">
                            <div class="max-w-xs ml-auto space-y-2 text-sm">
                                <div class="flex justify-between text-stone-500">
                                    <span>Subtotal</span>
                                    <span>₱{{ $order['subtotal'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between text-stone-500">
                                    <span>Delivery Fee</span>
                                    <span>{{ ($order['delivery_fee'] ?? 0) == 0 ? 'Free' : '₱' . $order['delivery_fee'] }}</span>
                                </div>
                                <div class="flex justify-between items-baseline pt-2 border-t border-stone-200">
                                    <span class="font-display font-bold text-oreo-noir">Total</span>
                                    <span class="font-display font-extrabold text-xl text-chocolate">₱{{ $order['total'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 md:px-6 md:py-5 flex flex-col sm:flex-row sm:justify-end gap-2">
                            <a href="/track?order={{ urlencode($order['order_number']) }}"
                               class="btn-secondary !py-2.5 text-sm justify-center">
                                <x-icon name="track" class="w-4 h-4" />
                                Track Order
                            </a>
                            @if($status === 'picked_up')
                                <a href="/menu" class="btn-primary !py-2.5 text-sm justify-center">
                                    <x-icon name="plus" class="w-4 h-4" />
                                    Order Again
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <nav id="ordersPaginator" aria-label="Pagination" class="flex items-center gap-1"></nav>

            <div id="ordersJumpWrapper" class="hidden items-center gap-2 sm:border-l sm:border-stone-200 sm:pl-4">
                <label for="ordersJumpInput" class="text-xs text-stone-500 whitespace-nowrap">Go to</label>
                <input id="ordersJumpInput" type="number" min="1" inputmode="numeric"
                       class="w-12 bg-white border border-stone-300 rounded-md px-2 py-1 text-center text-sm focus:ring-2 focus:ring-chocolate focus:border-transparent outline-none">
                <span id="ordersJumpTotal" class="text-xs text-stone-500 whitespace-nowrap"></span>
            </div>
        </div>

        <p id="ordersCounter" class="text-center text-xs text-stone-500 mt-3"></p>
    @endif
</section>

@endsection

@push('scripts')
<script>
(function () {
    const PER_PAGE = 5;
    const list = document.getElementById('ordersList');
    if (!list) return;

    const cards = Array.from(list.querySelectorAll('.order-card'));
    const total = cards.length;
    if (total === 0) return;

    const totalPages = Math.ceil(total / PER_PAGE);
    const paginator  = document.getElementById('ordersPaginator');
    const counter    = document.getElementById('ordersCounter');
    const jumpWrap   = document.getElementById('ordersJumpWrapper');
    const jumpInput  = document.getElementById('ordersJumpInput');
    const jumpTotal  = document.getElementById('ordersJumpTotal');

    let currentPage = 1;

    function render(page) {
        currentPage = Math.max(1, Math.min(page, totalPages));

        cards.forEach((card, i) => {
            const inPage = Math.floor(i / PER_PAGE) === currentPage - 1;
            card.style.display = inPage ? '' : 'none';
            const body = card.querySelector('.order-body');
            const chev = card.querySelector('.order-chevron');
            if (body) body.classList.add('hidden');
            if (chev) chev.classList.remove('rotate-180');
        });

        jumpInput.value = currentPage;
        jumpInput.max = totalPages;
        jumpTotal.textContent = `of ${totalPages}`;

        renderPaginator();
        renderCounter();
    }

    function renderPaginator() {
        paginator.innerHTML = '';

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
                window.scrollTo({ top: list.offsetTop - 80, behavior: 'smooth' });
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

        // Build page numbers
        const pages = [];
        if (totalPages <= 8) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            if (currentPage <= 4) {
                for (let i = 1; i <= 5; i++) pages.push(i);
                pages.push('…');
                pages.push(totalPages);
            } else if (currentPage >= totalPages - 3) {
                pages.push(1);
                pages.push('…');
                for (let i = totalPages - 4; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                pages.push('…');
                for (let i = currentPage - 1; i <= currentPage + 1; i++) pages.push(i);
                pages.push('…');
                pages.push(totalPages);
            }
        }

        pages.forEach(p => {
            if (p === '…') paginator.appendChild(ellipsis());
            else paginator.appendChild(makeBtn(p, p, { active: p === currentPage }));
        });

        // Next
        const nextIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>';
        paginator.appendChild(makeBtn(nextIcon, currentPage + 1, { disabled: currentPage === totalPages, icon: true }));

        // Show jump only when > 1 page
        jumpWrap.classList.toggle('hidden', totalPages <= 1);
        jumpWrap.classList.toggle('sm:flex', totalPages > 1);
    }

    function renderCounter() {
        const from = (currentPage - 1) * PER_PAGE + 1;
        const to   = Math.min(currentPage * PER_PAGE, total);
        counter.textContent = `Showing ${from}–${to} of ${total} order${total !== 1 ? 's' : ''}`;
    }

    function jumpToInputPage() {
        const target = parseInt(jumpInput.value, 10);
        if (isNaN(target)) { jumpInput.value = currentPage; return; }
        const clamped = Math.max(1, Math.min(target, totalPages));
        render(clamped);
        window.scrollTo({ top: list.offsetTop - 80, behavior: 'smooth' });
    }

    jumpInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); jumpToInputPage(); jumpInput.blur(); }
    });

    jumpInput.addEventListener('blur', () => {
        const target = parseInt(jumpInput.value, 10);
        if (!isNaN(target) && target !== currentPage) jumpToInputPage();
        else if (isNaN(target)) jumpInput.value = currentPage;
    });

    list.querySelectorAll('.order-header').forEach(header => {
        header.addEventListener('click', () => {
            const card = header.closest('.order-card');
            const body = card.querySelector('.order-body');
            const chev = card.querySelector('.order-chevron');
            body.classList.toggle('hidden');
            chev.classList.toggle('rotate-180');
        });
    });

    render(1);
})();
</script>
@endpush