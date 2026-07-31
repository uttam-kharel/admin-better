<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-12 md:py-16">
            <p class="text-eyebrow mb-3">Gallery</p>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">A look inside our hospital</h1>
        </div>
    </section>

    <section class="container-page py-10">
        <div class="flex flex-wrap gap-2 mb-8">
            <button type="button" wire:click="$set('selectedCategory', '')" class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ empty($selectedCategory) ? 'bg-primary text-primary-foreground' : 'bg-muted hover:bg-accent' }}">
                All
            </button>
            @foreach($categories as $cat)
                <button type="button" wire:click="$set('selectedCategory', '{{ $cat }}')" class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $selectedCategory === $cat ? 'bg-primary text-primary-foreground' : 'bg-muted hover:bg-accent' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
        <x-data.media-grid :cols="3">
            @forelse($items as $item)
                <figure class="group rounded-2xl overflow-hidden hairline bg-muted relative">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ $item->thumbnail ?? $item->url }}" alt="{{ $item->title }}" loading="lazy" class="size-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    </div>
                    <figcaption class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-foreground/80 to-transparent text-background text-sm font-medium">
                        {{ $item->title }}
                        @if($item->type === 'tour')
                            <span class="ml-2 text-xs bg-secondary rounded-full px-2 py-0.5">Virtual Tour</span>
                        @endif
                    </figcaption>
                </figure>
            @empty
                <x-feedback.empty-state title="No gallery items yet." />
            @endforelse
        </x-data.media-grid>
    </section>
</div>
