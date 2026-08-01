<?php

use Livewire\Component;
use App\Models\MenuItem;


new class extends Component
{
public string $search = '';
    public bool $modalOpen = false;
    public bool $creating = false;
    public ?int $editingId = null;
    public ?int $parentId = null;
    public array $form = [];
    public array $expanded = [];

    public function mount(): void
    {
        $this->expanded = MenuItem::whereNotNull('parent_id')->pluck('parent_id')->unique()->values()->toArray();
    }

    public function toggleExpand(int $id): void
    {
        $idx = array_search($id, $this->expanded);
        if ($idx !== false) {
            unset($this->expanded[$idx]);
        } else {
            $this->expanded[] = $id;
        }
        $this->expanded = array_values($this->expanded);
    }

    public function create(?int $parentId = null): void
    {
        $this->creating = true;
        $this->editingId = null;
        $this->parentId = $parentId;
        $this->form = [
            'title' => '',
            'slug' => '',
            'type' => 'link',
            'url' => '',
            'icon' => '',
            'description' => '',
            'parent_id' => $parentId ? (string) $parentId : '',
            'order' => '0',
        ];
        $this->modalOpen = true;
    }

    public function edit(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $this->creating = false;
        $this->editingId = $id;
        $this->parentId = null;
        $this->form = [
            'title' => $item->title,
            'slug' => $item->slug,
            'type' => $item->type,
            'url' => $item->url ?? '',
            'icon' => $item->icon ?? '',
            'description' => $item->description ?? '',
            'parent_id' => $item->parent_id ? (string) $item->parent_id : '',
            'order' => (string) ($item->order ?? 0),
        ];
        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->form = [];
    }

    public function save(): void
    {
        $this->validate([
            'form.title' => 'required|string|max:255',
            'form.slug' => 'required|string|max:255',
            'form.type' => 'required|in:link,dropdown,mega,external',
            'form.url' => 'nullable|string|max:500',
            'form.icon' => 'nullable|string|max:100',
            'form.description' => 'nullable|string|max:500',
            'form.parent_id' => 'nullable|string',
            'form.order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'title' => $this->form['title'],
            'slug' => $this->form['slug'],
            'type' => $this->form['type'],
            'url' => $this->form['url'] ?: null,
            'icon' => $this->form['icon'] ?: null,
            'description' => $this->form['description'] ?: null,
            'parent_id' => $this->form['parent_id'] ? (int) $this->form['parent_id'] : null,
            'order' => isset($this->form['order']) ? (int) $this->form['order'] : 0,
        ];

        if ($this->creating) {
            MenuItem::create($data);
            session()->flash('message', 'Created successfully.');
        } else {
            MenuItem::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Saved successfully.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        MenuItem::where('parent_id', $id)->delete();
        $item->delete();
        session()->flash('message', 'Deleted successfully.');
    }

    public function render()
    {
        $query = MenuItem::with('children');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%")
                  ->orWhere('url', 'like', "%{$this->search}%");
            });
        }
        $allItems = $query->orderBy('order')->get();
        $tree = $this->buildTree($allItems);
        $roots = $allItems->whereNull('parent_id')->where('id', '!=', $this->editingId);

        return $this->view([
            'tree' => $tree,
            'roots' => $roots,
            'allItems' => $allItems,
        ])->layout('layouts.admin', ['title' => 'Menus — Admin']);
    }

    private function buildTree($items): array
    {
        $map = [];
        $roots = [];
        foreach ($items as $item) {
            $itemArr = $item->toArray();
            $itemArr['children'] = [];
            $map[$item->id] = $itemArr;
        }
        foreach ($items as $item) {
            if ($item->parent_id && isset($map[$item->parent_id])) {
                $map[$item->parent_id]['children'][] = &$map[$item->id];
            }
        }
        foreach ($items as $item) {
            if (!$item->parent_id) {
                $roots[] = &$map[$item->id];
            }
        }
        return $roots;
    }
};

?>
<div class="space-y-5">
    <x-feedback.flash key="message" variant="success" />

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Navigation Menus</h2>
            <p class="text-sm text-muted-foreground mt-1">Manage top-level header menu entries and their nested items.</p>
        </div>
        <x-ui.button wire:click="create">
            @svg('lucide-plus', 'h-4 w-4') New top-level item
        </x-ui.button>
    </div>

    <div class="bg-surface rounded-xl border border-border overflow-hidden">
        @php $typeLabels = ['link' => 'Link', 'dropdown' => 'Dropdown', 'mega' => 'Mega menu', 'external' => 'External link']; @endphp
        @if(count($tree) === 0)
            <div class="p-12 text-center text-sm text-muted-foreground">No menu items yet.</div>
        @else
            <div class="divide-y divide-border">
                @foreach($tree as $parent)
                    @php $isOpen = in_array($parent['id'], $expanded); $hasChildren = count($parent['children']) > 0;
                        $typeBadge = match($parent['type']) {
                            'dropdown' => 'bg-amber-100 text-amber-800',
                            'mega' => 'bg-purple-100 text-purple-800',
                            'external' => 'bg-orange-100 text-orange-800',
                            default => 'bg-blue-100 text-blue-800',
                        };
                    @endphp
                    <div>
                        <div class="flex items-center gap-2 px-4 py-3 hover:bg-muted/30 transition-colors">
                            <button wire:click="toggleExpand({{ $parent['id'] }})" class="p-1 rounded hover:bg-muted text-muted-foreground" aria-label="{{ $isOpen ? 'Collapse' : 'Expand' }}">
                                @if($hasChildren)
                                    @svg('lucide-chevron-down', 'h-4 w-4 ' . ($isOpen ? '' : '-rotate-90'))
                                @else
                                    <span class="w-4 block"></span>
                                @endif
                            </button>
                            @svg('lucide-grip-vertical', 'h-4 w-4 text-muted-foreground/40 shrink-0')
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ $parent['title'] }}</span>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $typeBadge }}">{{ $typeLabels[$parent['type']] ?? $parent['type'] }}</span>
                                </div>
                                <div class="text-xs text-muted-foreground mt-0.5">
                                    /{{ $parent['slug'] }}
                                    @if($parent['url']) <span>· {{ $parent['url'] }}</span> @endif
                                    @if($hasChildren) <span>· {{ count($parent['children']) }} child{{ count($parent['children']) !== 1 ? 'ren' : '' }}</span> @endif
                                </div>
                            </div>
                            <button wire:click="create({{ $parent['id'] }})" class="p-1.5 rounded hover:bg-muted text-muted-foreground/70 hover:text-foreground text-xs" title="Add child item">
                                @svg('lucide-plus', 'h-3.5 w-3.5')
                            </button>
                            <button wire:click="edit({{ $parent['id'] }})" class="p-1.5 rounded hover:bg-muted text-muted-foreground/70 hover:text-foreground" aria-label="Edit">
                                @svg('lucide-pencil', 'h-4 w-4')
                            </button>
                            <button wire:click="delete({{ $parent['id'] }})" wire:confirm="Delete '{{ $parent['title'] }}'? {{ $hasChildren ? count($parent['children']) . ' child items will also be deleted.' : '' }}" class="p-1.5 rounded hover:bg-muted text-muted-foreground/70 hover:text-emergency" aria-label="Delete">
                                @svg('lucide-trash-2', 'h-4 w-4')
                            </button>
                        </div>

                        @if($isOpen && $hasChildren)
                            <div class="border-t border-border bg-muted/20">
                                @foreach($parent['children'] as $child)
                                    @php
                                        $childBadge = match($child['type']) {
                                            'dropdown' => 'bg-amber-100 text-amber-800',
                                            'mega' => 'bg-purple-100 text-purple-800',
                                            'external' => 'bg-orange-100 text-orange-800',
                                            default => 'bg-blue-100 text-blue-800',
                                        };
                                    @endphp
                                    <div class="flex items-center gap-2 px-4 py-2.5 pl-12 hover:bg-muted/30 transition-colors border-b border-border/50 last:border-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-muted-foreground/30 shrink-0"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm">{{ $child['title'] }}</span>
                                                <span class="text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $childBadge }}">{{ $typeLabels[$child['type']] ?? $child['type'] }}</span>
                                            </div>
                                            <div class="text-xs text-muted-foreground mt-0.5">
                                                /{{ $child['slug'] }}
                                                @if($child['url']) <span>· {{ $child['url'] }}</span> @endif
                                            </div>
                                        </div>
                                        <button wire:click="edit({{ $child['id'] }})" class="p-1.5 rounded hover:bg-muted text-muted-foreground/70 hover:text-foreground" aria-label="Edit">
                                            @svg('lucide-pencil', 'h-4 w-4')
                                        </button>
                                        <button wire:click="delete({{ $child['id'] }})" wire:confirm="Delete '{{ $child['title'] }}'?" class="p-1.5 rounded hover:bg-muted text-muted-foreground/70 hover:text-emergency" aria-label="Delete">
                                            @svg('lucide-trash-2', 'h-4 w-4')
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($modalOpen)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
            <button type="button" aria-label="Close" wire:click="closeModal" class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"></button>
            <div class="relative bg-surface rounded-t-2xl sm:rounded-2xl w-full sm:max-w-xl max-h-[90vh] overflow-hidden flex flex-col shadow-elevated animate-fade-up">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold">{{ $creating ? 'New' : 'Edit' }} menu item</h3>
                    <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground text-sm">Close</button>
                </div>
                <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <x-form.input variant="admin" label="Title" required wire:model="form.title" />
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <x-form.input variant="admin" label="Slug" required wire:model="form.slug" />
                        </div>
                        <div>
                            <x-form.select variant="admin" label="Type" required wire:model="form.type">
                                <option value="link">Link</option>
                                <option value="dropdown">Dropdown</option>
                                <option value="mega">Mega menu</option>
                                <option value="external">External link</option>
                            </x-form.select>
                        </div>
                        <div>
                            <x-form.select variant="admin" label="Parent" wire:model="form.parent_id">
                                <option value="">— Top-level —</option>
                                @foreach($roots as $root)
                                    <option value="{{ $root->id }}">{{ $root->title }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                    </div>

                    <x-form.input variant="admin" label="URL" wire:model="form.url" placeholder="/doctors or https://&hellip;" hint="Required for link and external types." />

                    <div class="grid grid-cols-2 gap-4">
                        <x-form.input variant="admin" label="Icon" wire:model="form.icon" placeholder="Heart, Brain, &hellip;" />
                        <x-form.input variant="admin" type="number" label="Order" wire:model="form.order" min="0" />
                    </div>

                    <x-form.textarea variant="admin" label="Description" wire:model="form.description" rows="2" />

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md text-sm font-medium hover:bg-muted">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 rounded-md text-sm font-semibold bg-primary text-primary-foreground shadow-card hover:opacity-90 disabled:opacity-60">
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Saving&hellip;</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
