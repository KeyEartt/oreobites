<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1A1A1A">
    <title>@yield('title', 'Admin — Oreo Bites')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-oreo-noir font-sans min-h-screen">

    {{-- Desktop sidebar (fixed) --}}
    <aside class="hidden lg:flex flex-col fixed inset-y-0 left-0 w-60 bg-oreo-noir text-milk-cream z-30">
        @include('dashboard.partials.sidebar-nav')
    </aside>

    {{-- Mobile drawer --}}
    <aside id="adminSidebarMobile"
           class="lg:hidden flex flex-col fixed inset-y-0 left-0 w-60 bg-oreo-noir text-milk-cream z-50
                  -translate-x-full transition-transform duration-300 ease-out">
        @include('dashboard.partials.sidebar-nav')
    </aside>
    <div id="adminSidebarOverlay"
         class="lg:hidden fixed inset-0 bg-oreo-noir/60 z-40 hidden"></div>

    {{-- Content --}}
    <div class="lg:ml-60 flex flex-col min-h-screen min-w-0">

        {{-- Top bar --}}
        <header class="sticky top-0 z-20 bg-white border-b border-stone-200">
            <div class="flex items-center gap-3 h-14 px-4 md:px-6">
                <button type="button" id="adminMenuToggle"
                        class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-cookie-brown hover:bg-milk-cream transition-colors shrink-0"
                        aria-label="Open menu">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>

                <div class="flex-1 min-w-0">
                    <h1 class="font-display font-bold text-base md:text-lg text-oreo-noir truncate">
                        @yield('page-title', 'Admin')
                    </h1>
                </div>

                <a href="/" class="text-xs text-cookie-brown hover:text-oreo-noir font-medium inline-flex items-center gap-1.5 shrink-0">
                    <x-icon name="arrow-right" class="w-3.5 h-3.5 rotate-180" />
                    <span class="hidden sm:inline">View site</span>
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-6 min-w-0">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <script>
    (function () {
        const toggle  = document.getElementById('adminMenuToggle');
        const sidebar = document.getElementById('adminSidebarMobile');
        const overlay = document.getElementById('adminSidebarOverlay');
        if (!toggle || !sidebar || !overlay) return;

        function open() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function close() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        toggle.addEventListener('click', open);
        overlay.addEventListener('click', close);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
    })();
    </script>
</body>
</html>