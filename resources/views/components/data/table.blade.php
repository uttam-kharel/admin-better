@props([
    'head' => null,
])

<div {{ $attributes->class('bg-surface rounded-xl border border-border overflow-hidden') }}>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            @isset($head)
                <thead class="bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                    <tr>{{ $head }}</tr>
                </thead>
            @endisset

            <tbody class="divide-y divide-border">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
