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

    @livewireScriptConfig
</body>
</html>
