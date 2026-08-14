<x-partials.base html-class="public-site" :title="$title ?? 'Shubham International Hospital — Advanced Medical Care for Every Generation'">
    <x-slot:head>
        <meta name="theme-color" content="#0a3b6f">
        <meta name="description" content="{{ $metaDescription ?? 'A multi-specialty hospital network combining clinical excellence with compassionate, patient-first care.' }}">

        <meta property="og:site_name" content="Shubham International Hospital" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary" />

        {{-- Reveal the page scrollbar only while scrolling, then fade it out after a pause. --}}
        <script>
            (function () {
                var timer = null;
                var root = document.documentElement;
                function hide() { root.classList.remove('scrolling'); }
                function show() {
                    root.classList.add('scrolling');
                    clearTimeout(timer);
                    timer = setTimeout(hide, 600);
                }
                window.addEventListener('scroll', show, { passive: true });
                window.addEventListener('wheel', show, { passive: true });
                window.addEventListener('touchmove', show, { passive: true });
            })();
        </script>
    </x-slot:head>

    <div class="flex min-h-dvh flex-col">
        {{-- Top Bar --}}
        <livewire:partials::top-bar />

        {{-- Header --}}
        <livewire:partials::header />

        {{-- Main Content --}}
        <main id="main" class="flex-1">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <livewire:partials::footer />
    </div>
</x-partials.base>
