<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ConnectSnap' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 antialiased">
    <div class="flex flex-col min-h-screen safe-area-inset">
        {{-- Header with Logo --}}
        <header class="flex justify-center pt-12 pb-8">
            <a href="/" class="flex items-center">
                <span class="text-3xl tracking-tight">
                    <span class="font-bold text-primary">Connect</span><span class="font-light text-zinc-700 dark:text-zinc-300">Snap</span>
                </span>
            </a>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col px-6">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="py-6 text-center">
            <flux:text size="sm" class="text-zinc-500">
                &copy; {{ date('Y') }} ConnectSnap. All rights reserved.
            </flux:text>
        </footer>
    </div>

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts
</body>
</html>