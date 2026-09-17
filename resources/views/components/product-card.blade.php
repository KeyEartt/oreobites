@props(['product'])

@php
    $inStock = ($product['stock'] ?? 0) > 0;
    $imageUrl = !empty($product['image_url']) ? asset($product['image_url']) : null;

    // Variant badge colors
    $variantStyles = [
        'Dark'  => 'bg-oreo-noir text-milk-cream',
        'White' => 'bg-milk-cream text-cookie-brown border border-stone-200',
        'Box'   => 'bg-chocolate text-milk-cream',
    ];
    $variantClass = $variantStyles[$product['variant']] ?? 'bg-stone-100 text-stone-700';
@endphp

<article class="group card-hover overflow-hidden flex flex-col"
         data-product-id="{{ $product['id'] }}">

    {{-- Image --}}
    <div class="relative aspect-square bg-milk-cream overflow-hidden">
        @if($imageUrl)
            <img src="{{ $imageUrl }}"
                 alt="{{ $product['name'] }}"
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        @else
            {{-- Fallback: branded cookie --}}
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-cookie-brown/10 to-oreo-noir/5">
                <x-icon name="cookie" class="w-20 h-20 text-cookie-brown/40" stroke-width="1" />
            </div>
        @endif

        {{-- Variant badge (top-left) --}}
        <span class="absolute top-3 left-3 badge {{ $variantClass }} backdrop-blur-sm">
            {{ $product['variant'] }}
        </span>

        {{-- Stock badge (top-right) --}}
        @if(!$inStock)
            <span class="absolute top-3 right-3 badge bg-red-100 text-red-700">
                Sold out
            </span>
        @elseif($product['stock'] <= 5)
            <span class="absolute top-3 right-3 badge bg-amber-100 text-amber-800">
                Only {{ $product['stock'] }} left
            </span>
        @endif
    </div>

    {{-- Body --}}
    <div class="flex flex-col flex-1 p-5">
        <div class="flex-1">
            <h3 class="font-display font-bold text-lg text-oreo-noir leading-tight">
                {{ $product['name'] }}
            </h3>

            <p class="text-sm text-stone-500 mt-1.5 line-clamp-2 leading-relaxed">
                {{ $product['description'] ?: 'A delicious Oreo cheesecake bite.' }}
            </p>

            @if($inStock)
                <div class="flex items-center gap-1.5 mt-3 text-xs text-green-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    In stock
                </div>
            @else
                <div class="flex items-center gap-1.5 mt-3 text-xs text-red-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Currently unavailable
                </div>
            @endif
        </div>

        {{-- Footer: price + button --}}
        <div class="flex items-end justify-between mt-5 pt-4 border-t border-stone-100">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold">Price</p>
                <p class="font-display font-bold text-2xl text-oreo-noir leading-none">
                    ₱{{ $product['price'] }}
                    <span class="text-xs text-stone-400 font-normal">/pc</span>
                </p>
            </div>

            <button type="button"
                    onclick='window.addToCart(@json($product))'
                    @disabled(!$inStock)
                    class="btn-primary !px-4 !py-2.5 text-sm
                           {{ !$inStock ? 'opacity-40 cursor-not-allowed' : '' }}">
                <x-icon name="plus" class="w-4 h-4" />
                {{ $inStock ? 'Add' : 'Sold out' }}
            </button>
        </div>
    </div>
</article>