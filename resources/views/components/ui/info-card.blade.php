@props([
    'icon',
    'title',
])

<div {{ $attributes->class('rounded-2xl bg-surface hairline p-6') }}>
    <h3 class="flex items-center gap-2 font-semibold mb-4 text-sm uppercase tracking-widest text-primary">
        @svg('lucide-' . $icon, 'h-4 w-4')
        {{ $title }}
    </h3>
    {{ $slot }}
</div>
