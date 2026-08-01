@props(['title' => 'Shubham International Hospital'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>{{ $title }}</title>

    {{-- Per-layout <head> extras: meta tags, robots, OG tags, etc. --}}
    {{ $head ?? '' }}

    <x-theme-colors />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-background text-foreground font-sans antialiased">
    {{ $slot }}

    {{-- Go-to-top button — appears after scrolling, safe across Livewire navigations --}}
    <button
        type="button"
        x-data="{ show: window.scrollY > 480 }"
        @scroll.window="show = window.scrollY > 480"
        @click="window.scrollTo({ top: 0, behavior: (window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth') })"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        x-cloak
        class="to-top-btn fixed bottom-6 right-6 z-50 grid size-12 place-items-center rounded-full bg-primary text-primary-foreground transition-transform duration-200 hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2"
        aria-label="Go to top"
    >
        @svg('lucide-arrow-up', 'h-5 w-5')
    </button>

    @livewireScriptConfig
</body>
</html>
