<?php

use Livewire\Component;
use App\Models\BlogPost;


new class extends Component
{
public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $post = BlogPost::with('authorInfo')->where('slug', $this->slug)->firstOrFail();
        $related = BlogPost::where('id', '!=', $post->id)->latest()->take(3)->get();

        return $this->view(['post' => $post, 'related' => $related]);
    }
};

?>
<div>
    <article>
        <div class="container-page py-10">
            <x-navigation.back-link :href="route('blogs.index')">Back to articles</x-navigation.back-link>
            <div class="max-w-3xl">
                <p class="text-eyebrow text-secondary">{{ $post->category }}</p>
                <h1 class="mt-3 text-3xl md:text-5xl font-bold tracking-tight text-balance">{{ $post->title }}</h1>
                <div class="mt-5 flex flex-wrap gap-5 text-sm text-muted-foreground">
                    @if($post->published_at)
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            {{ \Carbon\Carbon::parse($post->published_at)->format('M d, Y') }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $post->read_minutes }} min read
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        By {{ $post->author_name ?? $post->author }}
                    </span>
                </div>
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
                            <svg class="h-6 w-6 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
