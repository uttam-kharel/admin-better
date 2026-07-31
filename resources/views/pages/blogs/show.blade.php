<div>
    <article>
        <div class="container-page py-10">
            <x-navigation.back-link :href="route('blogs.index')">Back to articles</x-navigation.back-link>
            <div class="max-w-3xl">
                <p class="text-eyebrow text-secondary">{{ $post->category }}</p>
                <h1 class="mt-3 text-3xl md:text-5xl font-bold tracking-tight text-balance">{{ $post->title }}</h1>
                <x-ui.meta-bar class="mt-5" :items="[
                    ['icon' => 'calendar', 'value' => $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('M d, Y') : null],
                    ['icon' => 'clock', 'value' => $post->read_minutes . ' min read'],
                    ['icon' => 'user', 'value' => 'By ' . ($post->author_name ?? $post->author)],
                ]" />
            </div>
        </div>

        <div class="container-page">
            <div class="aspect-[16/9] rounded-3xl overflow-hidden hairline">
                <img src="{{ $post->image }}" alt="" class="size-full object-cover" />
            </div>
        </div>

        <div class="container-page py-12">
            <div class="max-w-3xl">
                <p class="text-xl text-muted-foreground italic mb-8 leading-relaxed">{{ $post->excerpt }}</p>
                <div class="prose prose-lg max-w-none">
                    {!! $post->content !!}
                </div>
            </div>

            @if($post->tags)
                <div class="max-w-3xl mt-10 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <x-ui.pill variant="tag">{{ $tag }}</x-ui.pill>
                    @endforeach
                </div>
            @endif
        </div>
    </article>

    @if($author)
        <section class="bg-surface-muted py-12">
            <div class="container-page">
                <div class="max-w-3xl mx-auto flex gap-6 p-6 rounded-2xl bg-surface hairline items-start">
                    @if($author->photo)
                        <img src="{{ $author->photo }}" alt="{{ $author->name }}" class="size-16 rounded-full object-cover shrink-0" />
                    @else
                        <div class="size-16 rounded-full bg-muted grid place-items-center shrink-0">
                            @svg('lucide-user', 'h-6 w-6 text-muted-foreground')
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold">{{ $author->name }}</p>
                        @if($author->specialty)
                            <p class="text-sm text-secondary">{{ $author->specialty }}</p>
                        @endif
                        @if($author->bio)
                            <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $author->bio }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($related && $related->count() > 0)
        <section class="py-12">
            <div class="container-page">
                <h2 class="text-2xl font-bold mb-6">Related articles</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($related as $r)
                        <x-ui.media-card :href="route('blogs.show', $r->slug)" :src="$r->image" :alt="$r->title" aspect="16/10">
                            <div class="p-5">
                                <p class="text-xs text-muted-foreground mb-1">By {{ $r->author_name ?? $r->author }}</p>
                                <h3 class="font-semibold group-hover:text-primary transition-colors text-balance">{{ $r->title }}</h3>
                            </div>
                        </x-ui.media-card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
