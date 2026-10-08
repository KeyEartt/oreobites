@php
    $user = session('auth_user');
    $role = $user['role'] ?? 'customer';
@endphp

<div class="flex flex-col h-full">

    {{-- Brand --}}
    <div class="px-5 py-5 border-b border-white/10 shrink-0">
        <a href="/" class="flex items-center gap-2.5">
            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-chocolate text-milk-cream shrink-0">
                <x-icon name="cookie" class="w-5 h-5" />
            </span>
            <div class="min-w-0">
                <p class="font-display font-bold text-base leading-tight">
                    <span class="text-golden">Oreo</span><span class="text-white">Bites</span>
                </p>
                <p class="text-[10px] uppercase tracking-wider text-stone-500 font-bold">
                    {{ $role === 'admin' ? 'Admin Panel' : 'Staff Panel' }}
                </p>
            </div>
        </a>
    </div>

    {{-- User chip --}}
    <div class="px-5 py-4 border-b border-white/10 flex items-center gap-3 shrink-0">
        <div class="w-9 h-9 rounded-full bg-chocolate flex items-center justify-center text-milk-cream text-sm font-bold shrink-0">
            {{ strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-white truncate">{{ $user['full_name'] ?? 'User' }}</p>
            <p class="text-[10px] uppercase tracking-wider text-golden font-bold">{{ $role }}</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        @if($role === 'admin')
            <p class="px-3 pb-2 text-[10px] uppercase tracking-wider text-stone-500 font-bold">Operations</p>

            <a href="/admin#overview"
               class="admin-nav-item group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-300 hover:bg-white/5 hover:text-white transition-colors"
               data-tab-link="overview">
                <x-icon name="admin" class="w-4 h-4 shrink-0" />
                <span>Overview</span>
            </a>
            <a href="/admin#products"
               class="admin-nav-item group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-300 hover:bg-white/5 hover:text-white transition-colors"
               data-tab-link="products">
                <x-icon name="cookie" class="w-4 h-4 shrink-0" />
                <span>Products</span>
            </a>
            <a href="/admin#orders"
               class="admin-nav-item group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-300 hover:bg-white/5 hover:text-white transition-colors"
               data-tab-link="orders">
                <x-icon name="orders" class="w-4 h-4 shrink-0" />
                <span>Orders</span>
            </a>
        @endif

        <p class="px-3 pt-4 pb-2 text-[10px] uppercase tracking-wider text-stone-500 font-bold">
            {{ $role === 'admin' ? 'Tools' : 'Operations' }}
        </p>

        <a href="/staff"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-300 hover:bg-white/5 hover:text-white transition-colors {{ request()->is('staff') ? 'bg-chocolate text-white' : '' }}">
            <x-icon name="staff" class="w-4 h-4 shrink-0" />
            <span>Kitchen Display</span>
            @if(request()->is('staff'))
                <span class="ml-auto inline-flex w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            @endif
        </a>
    </nav>

    {{-- Footer --}}
    <div class="px-3 py-4 border-t border-white/10 space-y-1 shrink-0">
        <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-300 hover:bg-white/5 hover:text-white transition-colors">
            <x-icon name="home" class="w-4 h-4 shrink-0" />
            <span>View Site</span>
        </a>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-300 hover:bg-red-500/10 hover:text-red-200 transition-colors">
                <x-icon name="logout" class="w-4 h-4 shrink-0" />
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>