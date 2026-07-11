<x-partials.base :title="$title ?? 'Shubham International Hospital'">
    <x-slot:head>
        <meta name="description" content="{{ $metaDescription ?? 'A multi-specialty hospital network combining clinical excellence with compassionate, patient-first care.' }}">
    </x-slot:head>

    <div class="flex min-h-dvh flex-col">
        <livewire:partials::top-bar />

        <livewire:partials::header />

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-muted/20">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-partials.base>
