@props([
    'label' => null,
    'error' => false,
    'hint' => null,
    'required' => false,
    'variant' => 'default',
    'options' => [],
    'model' => null,
    'value' => '',
    'placeholder' => '— Select —',
    'searchable' => true,
    'id' => null,
])

@php
    $id = $id ?? 'select-menu-' . \Illuminate\Support\Str::random(6);
    $currentLabel = collect($options)->firstWhere('value', (string) $value)['label'] ?? null;
    $jsonOptions = json_encode(array_values($options), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $showSearch = $searchable && count($options) > 6;
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $id }}" class="block {{ $variant === 'admin' ? 'text-xs font-semibold text-foreground/80' : 'text-sm font-medium mb-1' }}">
            {{ $label }}
            @if($required)
                <span class="text-destructive">*</span>
            @endif
        </label>
    @endif

    <div
        x-data="{
            open: false,
            search: '',
            value: @js((string) $value),
            label: @js($currentLabel ?? ''),
            options: {!! $jsonOptions !!},
            set(v, l) {
                this.value = String(v)
                this.label = l
                this.open = false
                this.search = ''
                @if($model)
                    $wire.set(@js($model), v)
                @endif
            },
            get filtered() {
                const q = this.search.toLowerCase()
                return this.options.filter(o => !q || String(o.label).toLowerCase().includes(q))
            },
        }"
        class="relative"
    >
        <button
            type="button"
            :id="@js($id)"
            @click="open = !open"
            @keydown.escape="open = false"
            aria-haspopup="listbox"
            :aria-expanded="open"
            :aria-label="label || @js($placeholder)"
            class="w-full flex items-center justify-between gap-2 text-left text-sm bg-background border focus:outline-none focus:ring-2 focus:ring-primary/30 transition {{ $variant === 'admin' ? 'px-3 py-2 rounded-md' : 'px-3.5 py-2.5 rounded-md' }} {{ $error ? 'border-destructive' : 'border-border' }}"
        >
            <span class="truncate" :class="label ? 'text-foreground' : 'text-muted-foreground'">
                <span x-text="label || @js($placeholder)">{{ $currentLabel ?? $placeholder }}</span>
            </span>
            <svg class="h-4 w-4 shrink-0 text-muted-foreground transition-transform duration-200" :class="open && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        </button>

        <div
            x-show="open"
            x-cloak
            @click.outside="open = false"
            class="absolute z-30 mt-1.5 w-full min-w-[200px] rounded-lg border border-border bg-popover shadow-elevated overflow-hidden animate-fade-up"
            role="listbox"
        >
            @if($showSearch)
                <div class="p-2 border-b border-border">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search…"
                        class="w-full px-2.5 py-1.5 text-sm rounded-md bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30"
                        @keydown.escape.stop="open = false"
                    />
                </div>
            @endif
            <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5" role="listbox">
                <template x-for="opt in filtered" :key="opt.value">
                    <li role="option" :aria-selected="String(opt.value) === value">
                        <button
                            type="button"
                            @click="set(opt.value, opt.label)"
                            class="w-full text-left px-3 py-2 text-sm rounded-md transition-colors"
                            :class="String(opt.value) === value ? 'bg-primary-soft text-primary font-semibold' : 'text-foreground/80 hover:bg-muted'"
                        >
                            <span x-text="opt.label"></span>
                        </button>
                    </li>
                </template>
                <li x-show="filtered.length === 0" class="px-3 py-2 text-sm text-muted-foreground">No matches</li>
            </ul>
        </div>
    </div>

    @if($error)
        <p class="text-xs text-destructive">{{ is_string($error) ? $error : 'This field is required.' }}</p>
    @elseif($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
