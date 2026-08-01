<?php

namespace App\Livewire\Pages;

use App\Models\BlogPost;
use Livewire\Component;

class BlogsShow extends Component
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

        return view('pages.blogs.show', [
            'post' => $post,
            'related' => $related,
            'author' => $post->authorInfo,
        ]);
    }
}
