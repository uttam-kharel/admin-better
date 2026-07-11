@props([
    'photo' => null,
    'name' => null,
    'href' => null,
    'designation' => null,
    'avatar' => false,
])

<a href="{{ $href }}" {{ $attributes->class('group flex gap-4 items-center rounded-2xl hairline bg-surface p-4') }}>
    @if($avatar)
        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy" class="size-16 rounded-full object-cover" />
    @else
        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy" class="size-10 rounded-lg object-cover" />
    @endif
    <div>
        <h3 class="font-semibold group-hover:text-primary transition-colors">{{ $name }}</h3>
        <p class="text-xs text-muted-foreground">{{ $designation }}</p>
    </div>
</a>
