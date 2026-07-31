<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-12 md:py-16">
            <p class="text-eyebrow mb-3">Health Library</p>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">Insights from our doctors</h1>
            <div class="mt-7 max-w-md">
                <x-form.search-input
                    wire:model.live.debounce="search"
                    placeholder="Search articles…"
                    aria-label="Search articles"
                />
            </div>
        </div>
    </section>

    <section class="container-page py-12">
        @if($blogs->count() === 0)
            <x-feedback.empty-state title="No articles match your search." />
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($blogs as $post)
                    <x-ui.media-card :href="route('blogs.show', $post->slug)" :src="$post->image" :alt="$post->title" aspect="16/10">
                        <div class="p-6">
                            <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                <span class="text-secondary font-semibold">{{ $post->category }}</span>
                                <span>&middot;</span>
                                <span>{{ $post->read_minutes }} min read</span>
                                @if($post->author_name)
                                    <span>&middot;</span>
                                    <span>{{ $post->author_name }}</span>
                                @endif
                            </div>
                            <h3 class="font-semibold mt-3 group-hover:text-primary transition-colors text-balance">{{ $post->title }}</h3>
                            <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $post->excerpt }}</p>
                        </div>
                    </x-ui.media-card>
                @endforeach
            </div>
        @endif
    </section>
</div>
