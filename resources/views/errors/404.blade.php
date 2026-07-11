<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a3b6f">
    <title>404 &mdash; Shubham International Hospital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-background text-foreground font-sans antialiased">
    <div class="flex min-h-dvh items-center justify-center bg-background px-4">
        <div class="max-w-md text-center">
            <h1 class="text-xl font-semibold tracking-tight text-foreground">
                This page didn't load
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                Something went wrong on our end. You can try refreshing or head back home.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <a href="{{ url()->current() }}"
                   class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:opacity-90">
                    Try again
                </a>
                <a href="/"
                   class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent">
                    Go home
                </a>
            </div>
        </div>
    </div>
</body>
</html>