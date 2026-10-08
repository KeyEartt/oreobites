@forelse($orders as $order)
    @php
        $status = $order['status'] ?? 'pending';
        $method = $order['payment_method'] ?? 'online';

        $statusStyles = [
            'pending'   => [
                'border' => 'border-l-amber-500',
                'badge'  => 'bg-amber-100 text-amber-800',
                'dot'    => 'bg-amber-500',
            ],
            'paid' => [
                'border' => 'border-l-amber-500',
                'badge'  => 'bg-amber-100 text-amber-800',
                'dot'    => 'bg-amber-500',
            ],
            'preparing' => [
                'border' => 'border-l-blue-500',
                'badge'  => 'bg-blue-100 text-blue-800',
                'dot'    => 'bg-blue-500',
            ],
            'ready' => [
                'border' => 'border-l-green-500',
                'badge'  => 'bg-green-100 text-green-800',
                'dot'    => 'bg-green-500',
            ],
        ];
        $style = $statusStyles[$status] ?? [
            'border' => 'border-l-stone-300',
            'badge'  => 'bg-stone-100 text-stone-700',
            'dot'    => 'bg-stone-400',
        ];

        $isPendingInPerson = $status === 'pending' && $method === 'in_person';

        // Status label — pending online says "Unpaid Online", pending in-person says "Awaiting Payment"
        $displayStatus = $status;
        if ($isPendingInPerson) {
            $displayStatus = 'awaiting_payment';
        }
    @endphp

    <article class="order-card card overflow-hidden border-l-4 {{ $style['border'] }} flex flex-col"
             data-order-id="{{ $order['id'] }}"
             data-status="{{ $status }}"
             data-payment-method="{{ $method }}">

        {{-- Header --}}
        <div class="p-4 border-b border-stone-100">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <p class="font-mono font-bold text-chocolate text-sm truncate">{{ $order['order_number'] }}</p>
                    <p class="text-[11px] text-stone-500 mt-0.5 flex items-center gap-1.5">
                        <x-icon name="clock" class="w-3 h-3" />
                        {{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}
                    </p>
                </div>
                <span class="badge {{ $style['badge'] }} shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                    {{ strtoupper(str_replace('_', ' ', $displayStatus)) }}
                </span>
            </div>

            {{-- Payment method badge --}}
            @if($method === 'in_person')
                <div class="mt-2 inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 rounded-full px-2 py-0.5">
                    <x-icon name="credit-card" class="w-3 h-3" />
                    Pay in person · ₱{{ $order['total'] }}
                </div>
            @endif
        </div>

        {{-- Customer --}}
        <div class="p-4 border-b border-stone-100 space-y-2">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-milk-cream text-cookie-brown flex items-center justify-center text-xs font-bold shrink-0">
                    {{ strtoupper(substr($order['customer_name'], 0, 1)) }}
                </div>
                <p class="font-semibold text-oreo-noir text-sm truncate">{{ $order['customer_name'] }}</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-stone-500">
                <x-icon name="login" class="w-3.5 h-3.5 shrink-0" />
                <span class="truncate">{{ $order['customer_phone'] }}</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-stone-500">
                <x-icon name="location" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                <span class="leading-snug">
                    @if($order['delivery_type'] === 'pickup')
                        Campus Pickup — UCC Congressional
                    @else
                        {{ ucfirst(str_replace('_', ' ', $order['delivery_type'])) }} — {{ $order['delivery_address'] }}
                    @endif
                </span>
            </div>
            @if($order['eta'])
                <div class="flex items-center gap-2 text-xs text-stone-500">
                    <x-icon name="clock" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ $order['eta'] }}</span>
                </div>
            @endif
        </div>

        {{-- Items --}}
        <div class="p-4 border-b border-stone-100 flex-1">
            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-bold mb-2">
                Items ({{ collect($order['items'])->sum('quantity') }})
            </p>
            <ul class="space-y-1.5">
                @foreach($order['items'] as $item)
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-cookie-brown truncate pr-2">
                            <span class="font-bold text-oreo-noir">{{ $item['quantity'] ?? 1 }}×</span>
                            {{ $item['name'] ?? 'Item' }}
                        </span>
                        <span class="text-xs text-stone-500 shrink-0">₱{{ ($item['price'] ?? 0) * ($item['quantity'] ?? 1) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Total --}}
        <div class="px-4 py-3 bg-milk-cream/40 border-b border-stone-100 flex justify-between items-center">
            <span class="text-xs text-stone-500 uppercase tracking-wider font-bold">Total</span>
            <span class="font-display font-bold text-lg text-chocolate">₱{{ $order['total'] }}</span>
        </div>

        {{-- Actions --}}
        <div class="p-4 space-y-2">
            @if($isPendingInPerson)
                <button type="button" onclick="markPaid('{{ $order['id'] }}')"
                        class="w-full inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                    <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                    Confirm Payment Received
                </button>
            @elseif($status === 'paid')
                <button type="button" onclick="updateStatus('{{ $order['id'] }}', 'preparing')"
                        class="btn-primary w-full !py-2.5 text-sm">
                    <x-icon name="staff" class="w-4 h-4" />
                    Start Preparing
                </button>
            @elseif($status === 'preparing')
                <button type="button" onclick="updateStatus('{{ $order['id'] }}', 'ready')"
                        class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                    <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                    Mark Ready for Pickup
                </button>
            @elseif($status === 'ready')
                <button type="button" onclick="updateStatus('{{ $order['id'] }}', 'picked_up')"
                        class="w-full inline-flex items-center justify-center gap-2 bg-oreo-noir hover:bg-cookie-brown text-white font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                    <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                    Picked Up
                </button>
            @endif

            @if(!$isPendingInPerson)
                <button type="button" onclick="updateStatus('{{ $order['id'] }}', 'cancelled')"
                        class="w-full text-red-600 hover:bg-red-50 py-2 rounded-lg text-xs font-medium transition-colors">
                    Cancel Order
                </button>
            @else
                <button type="button" onclick="updateStatus('{{ $order['id'] }}', 'cancelled')"
                        class="w-full text-red-600 hover:bg-red-50 py-2 rounded-lg text-xs font-medium transition-colors">
                    Cancel Reservation
                </button>
            @endif
        </div>
    </article>
@empty
    <div class="col-span-full text-center py-16 md:py-20">
        <div class="w-20 h-20 mx-auto rounded-full bg-milk-cream flex items-center justify-center text-cookie-brown">
            <x-icon name="cookie" class="w-9 h-9" stroke-width="1.5" />
        </div>
        <p class="font-display font-bold text-xl text-oreo-noir mt-5">No active orders</p>
        <p class="text-stone-500 text-sm mt-1">New paid orders will appear here automatically.</p>
    </div>
@endforelse