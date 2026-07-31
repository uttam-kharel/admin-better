<?php

namespace App\Livewire\Pages;

use App\Models\BlogPost;
use Livewire\Component;
use Livewire\WithPagination;

class BlogsIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 12;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = BlogPost::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('excerpt', 'like', "%{$this->search}%")
                  ->orWhere('category', 'like', "%{$this->search}%");
            });
        }

        $blogs = $query->latest('published_at')->paginate($this->perPage);

        return view('pages.blogs.index', [
            'blogs' => $blogs,
        ]);
    }
}
