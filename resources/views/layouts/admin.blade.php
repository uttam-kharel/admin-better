<x-partials.base :title="$title ?? 'Admin — Shubham International Hospital'">
    <x-slot:head>
        <meta name="robots" content="noindex, nofollow">
    </x-slot:head>

    <div class="flex h-dvh overflow-hidden">
        @livewire('admin::sidebar.index')

        <div class="flex-1 flex flex-col min-w-0">
            @livewire('admin::topbar.index')

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-muted/20">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-partials.base>
