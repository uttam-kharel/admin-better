<?php

use Livewire\Component;
use App\Models\CmsPage;


new class extends Component
{
public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $page = CmsPage::where('slug', $this->slug)->firstOrFail();
        return $this->view(['page' => $page]);
    }
};

?>
<div>
    <article>
        @if($page->blocks)
            @foreach($page->blocks as $block)
                <x-sections.content-block :block="$block" :title="$page->title" />
            @endforeach
        @else
            <section class="container-page section-y">
                <div class="max-w-3xl mx-auto">
                    <p class="text-muted-foreground">This page is being updated. Check back soon.</p>
                </div>
            </section>
        @endif
    </article>
</div>
