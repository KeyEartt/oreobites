@php
    $user = session('auth_user');
    $currentPath = trim(request()->path(), '/');

    // Active state helper: exact match or nested path
    $isActive = function ($target) use ($currentPath) {
        $target = trim($target, '/');
        if ($target === '') return $currentPath === '';
        return $currentPath === $target || str_starts_with($currentPath, $target . '/');
    };

    // Link class helper — active gets a distinct look + underline
    $linkClass = function ($target) use ($isActive) {
        $base = 'relative px-3 py-2 rounded-lg text-sm font-medium transition-colors';
        return $isActive($target)
            ? $base . ' text-oreo-noir'
            : $base . ' text-cookie-brown hover:text-oreo-noir hover:bg-milk-cream';
    };
@endphp

<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-sm border-b border-stone-200">
    <nav class="container-app">
        <div class="flex justify-between items-center h-16">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2 group">
                <span class="w-9 h-9 flex items-center justify-center rounded-full bg-oreo-noir text-milk-cream group-hover:bg-cookie-brown transition-colors">
                    <x-icon name="cookie" class="w-5 h-5" />
                </span>
                <span class="font-display font-bold text-lg tracking-tight">
                    <span class="text-chocolate">Oreo</span><span class="text-oreo-noir">Bites</span>
                </span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="/" class="{{ $linkClass('') }}">
                    Home
                    @if($isActive(''))
                        <span class="absolute left-3 right-3 -bottom-px h-0.5 bg-golden rounded-full"></span>
                    @endif
                </a>
                <a href="/menu" class="{{ $linkClass('/menu') }}">
                    Menu
                    @if($isActive('/menu'))
                        <span class="absolute left-3 right-3 -bottom-px h-0.5 bg-golden rounded-full"></span>
                    @endif
                </a>
                <a href="/track" class="{{ $linkClass('/track') }}">
                    Track Order
                    @if($isActive('/track'))
                        <span class="absolute left-3 right-3 -bottom-px h-0.5 bg-golden rounded-full"></span>
                    @endif
                </a>

                @if($user)
                    @if($user['role'] === 'customer')
                        <a href="/my-orders" class="{{ $linkClass('/my-orders') }}">
                            My Orders
                            @if($isActive('/my-orders'))
                                <span class="absolute left-3 right-3 -bottom-px h-0.5 bg-golden rounded-full"></span>
                            @endif
                        </a>
                    @endif
                    @if(in_array($user['role'], ['staff', 'admin']))
                        <a href="/staff" class="{{ $linkClass('/staff') }}">
                            Staff
                            @if($isActive('/staff'))
                                <span class="absolute left-3 right-3 -bottom-px h-0.5 bg-golden rounded-full"></span>
                            @endif
                        </a>
                    @endif
                    @if($user['role'] === 'admin')
                        <a href="/admin" class="{{ $linkClass('/admin') }}">
                            Admin
                            @if($isActive('/admin'))
                                <span class="absolute left-3 right-3 -bottom-px h-0.5 bg-golden rounded-full"></span>
                            @endif
                        </a>
                    @endif
                @endif
            </div>

            {{-- Right side: Auth + Cart --}}
            <div class="flex items-center gap-2">
                @if($user)
                    <div class="hidden md:flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-full bg-milk-cream border border-stone-200">
                        <div class="w-6 h-6 rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($user['full_name'], 0, 1)) }}
                        </div>
                        <span class="text-xs font-medium text-cookie-brown max-w-[100px] truncate">
                            {{ explode(' ', $user['full_name'])[0] }}
                        </span>
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit" class="text-stone-400 hover:text-red-600 transition-colors" title="Logout">
                                <x-icon name="logout" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-cookie-brown hover:text-oreo-noir hover:bg-milk-cream transition-colors">
                        <x-icon name="login" class="w-4 h-4" />
                        Login
                    </a>
                @endif

                {{-- Cart --}}
                <button id="cartToggle" type="button"
                        class="relative w-10 h-10 flex items-center justify-center rounded-full bg-oreo-noir text-white hover:bg-cookie-brown transition-colors"
                        aria-label="Open cart">
                    <x-icon name="cart" class="w-5 h-5" />
                    <span id="cartBadge"
                          class="absolute -top-1 -right-1 bg-chocolate text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center hidden">
                        0
                    </span>
                </button>

                {{-- Mobile menu toggle --}}
                <button id="mobileMenuToggle" type="button"
                        class="md:hidden w-10 h-10 flex items-center justify-center rounded-full text-cookie-brown hover:bg-milk-cream transition-colors"
                        aria-label="Toggle menu">
                    <x-icon name="menu" class="w-5 h-5" id="mobileMenuIconOpen" />
                    <x-icon name="close" class="w-5 h-5 hidden" id="mobileMenuIconClose" />
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="md:hidden hidden border-t border-stone-200 py-3">
            <div class="space-y-1">
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ $isActive('') ? 'bg-milk-cream text-oreo-noir' : 'text-cookie-brown hover:bg-milk-cream' }}">
                    <x-icon name="home" class="w-4 h-4" /> Home
                    @if($isActive(''))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-golden"></span>
                    @endif
                </a>
                <a href="/menu" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ $isActive('/menu') ? 'bg-milk-cream text-oreo-noir' : 'text-cookie-brown hover:bg-milk-cream' }}">
                    <x-icon name="menu-bag" class="w-4 h-4" /> Menu
                    @if($isActive('/menu'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-golden"></span>
                    @endif
                </a>
                <a href="/track" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ $isActive('/track') ? 'bg-milk-cream text-oreo-noir' : 'text-cookie-brown hover:bg-milk-cream' }}">
                    <x-icon name="track" class="w-4 h-4" /> Track Order
                    @if($isActive('/track'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-golden"></span>
                    @endif
                </a>

                @if($user)
                    @if($user['role'] === 'customer')
                        <a href="/my-orders" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ $isActive('/my-orders') ? 'bg-milk-cream text-oreo-noir' : 'text-cookie-brown hover:bg-milk-cream' }}">
                            <x-icon name="orders" class="w-4 h-4" /> My Orders
                            @if($isActive('/my-orders'))
                                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-golden"></span>
                            @endif
                        </a>
                    @endif
                    @if(in_array($user['role'], ['staff', 'admin']))
                        <a href="/staff" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ $isActive('/staff') ? 'bg-milk-cream text-oreo-noir' : 'text-cookie-brown hover:bg-milk-cream' }}">
                            <x-icon name="staff" class="w-4 h-4" /> Staff
                            @if($isActive('/staff'))
                                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-golden"></span>
                            @endif
                        </a>
                    @endif
                    @if($user['role'] === 'admin')
                        <a href="/admin" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ $isActive('/admin') ? 'bg-milk-cream text-oreo-noir' : 'text-cookie-brown hover:bg-milk-cream' }}">
                            <x-icon name="admin" class="w-4 h-4" /> Admin
                            @if($isActive('/admin'))
                                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-golden"></span>
                            @endif
                        </a>
                    @endif

                    <div class="pt-2 mt-2 border-t border-stone-200 flex items-center justify-between px-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($user['full_name'], 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-cookie-brown">{{ $user['full_name'] }}</span>
                        </div>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="flex items-center gap-1.5 text-xs font-medium text-red-600 hover:text-red-800">
                                <x-icon name="logout" class="w-4 h-4" /> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-cookie-brown hover:bg-milk-cream transition-colors">
                        <x-icon name="login" class="w-4 h-4" /> Login
                    </a>
                @endif
            </div>
        </div>
    </nav>
</header>

{{-- Cart Drawer (unchanged structure, brand colors updated) --}}
<div id="cartDrawer" class="fixed inset-0 z-50 hidden">
    <div id="cartOverlay" class="absolute inset-0 bg-oreo-noir/40 opacity-0 transition-opacity"></div>

    <aside id="cartPanel"
           class="absolute right-0 top-0 h-full w-full max-w-md bg-milk-cream shadow-2xl
                  transform translate-x-full transition-transform duration-300
                  flex flex-col">

        <header class="flex items-center justify-between px-5 py-4 border-b border-stone-200 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-oreo-noir text-milk-cream flex items-center justify-center">
                    <x-icon name="cart" class="w-4 h-4" />
                </div>
                <div>
                    <h2 class="font-display font-bold text-oreo-noir">Your Cart</h2>
                    <p class="text-xs text-stone-500">Fresh from the kitchen</p>
                </div>
            </div>
            <button id="cartClose" type="button"
                    class="w-9 h-9 flex items-center justify-center rounded-full text-stone-500 hover:bg-milk-cream hover:text-oreo-noir transition-colors"
                    aria-label="Close cart">
                <x-icon name="close" class="w-5 h-5" />
            </button>
        </header>

        <div id="cartItems" class="flex-1 overflow-y-auto p-5 space-y-3">
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-white border border-stone-200 flex items-center justify-center text-stone-400">
                    <x-icon name="cart" class="w-7 h-7" />
                </div>
                <p class="text-cookie-brown font-medium mt-4">Your cart is empty</p>
                <p class="text-stone-500 text-sm mt-1">Add a few bites to get started</p>
            </div>
        </div>

        <footer id="cartFooter" class="border-t border-stone-200 bg-white p-5 hidden">
            <div class="flex justify-between items-baseline mb-4">
                <span class="text-sm text-stone-500">Subtotal</span>
                <span id="cartTotal" class="font-display font-bold text-2xl text-oreo-noir">₱0</span>
            </div>
            <a href="/checkout" class="btn-primary w-full">
                Checkout
                <x-icon name="arrow-right" class="w-4 h-4" />
            </a>
            <button type="button" id="cartContinue"
                    class="w-full text-center text-xs text-stone-500 hover:text-cookie-brown mt-3 font-medium">
                Continue shopping
            </button>
        </footer>
    </aside>
</div>

@push('scripts')
<script>
(function () {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const iconOpen = document.getElementById('mobileMenuIconOpen');
    const iconClose = document.getElementById('mobileMenuIconClose');

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', () => {
            const open = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden', open);
            iconClose.classList.toggle('hidden', !open);
        });
    }

    const continueBtn = document.getElementById('cartContinue');
    if (continueBtn) {
        continueBtn.addEventListener('click', () => {
            const closeBtn = document.getElementById('cartClose');
            if (closeBtn) closeBtn.click();
        });
    }
})();
</script>
@endpush