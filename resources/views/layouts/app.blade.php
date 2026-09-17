<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="@yield('description', 'Oreo Cheesecake Bites — bite-sized frozen desserts glazed in chocolate. Order online for campus pickup.')">
    <meta name="theme-color" content="#1A1A1A">

    {{-- Open Graph (for Messenger / Facebook sharing) --}}
    <meta property="og:title" content="@yield('title', 'Oreo Bites — Cheesecake Treats')">
    <meta property="og:description" content="@yield('description', 'Bite-sized frozen desserts glazed in chocolate.')">
    <meta property="og:type" content="website">

    <title>@yield('title', 'Oreo Bites — Cheesecake Treats')</title>

    {{-- Google Fonts: Outfit (display), Inter (body), JetBrains Mono (order numbers) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-milk-cream text-oreo-noir min-h-screen flex flex-col font-sans">
    @include('partials.navbar')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>