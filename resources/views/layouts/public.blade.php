<x-partials.base :title="$title ?? 'Shubham International Hospital — Advanced Medical Care for Every Generation'">
    <x-slot:head>
        <meta name="theme-color" content="#0a3b6f">
        <meta name="description" content="{{ $metaDescription ?? 'A multi-specialty hospital network combining clinical excellence with compassionate, patient-first care.' }}">

        <meta property="og:site_name" content="Shubham International Hospital" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary" />
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
