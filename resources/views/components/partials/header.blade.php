<?php

use App\Models\SiteSetting;
use Livewire\Component;
use App\Models\MenuItem;


new class extends Component
{
public function render()
    {
        $menus = MenuItem::with('children')->whereNull('parent_id')->orderBy('order')->get();
        $settings = SiteSetting::first();
        $header = $settings?->header ?? [];

        return $this->view([
            'menus' => $menus,
            'header' => $header,
            'settings' => $settings,
        ]);
    }
};

?>
<header
    x-data="{
        mobileOpen: false,
        openSections: [],
        toggleSection(id) {
            const idx = this.openSections.indexOf(id)
            if (idx > -1) {
                this.openSections.splice(idx, 1)
            } else {
                this.openSections.push(id)
            }
        },
        isOpen(id) {
            return this.openSections.indexOf(id) > -1
        },
        closeMenu() {
            this.mobileOpen = false
            this.openSections = []
        },
        init() {
            this.$watch('mobileOpen', (value) => {
                if (value) {
                    document.body.style.overflow = 'hidden'
                } else {
                    document.body.style.overflow = ''
                }
            })
        }
    }"
    x-cloak
    class="site-header sticky top-10 z-40 bg-background/85 backdrop-blur-md border-b border-border transition-[box-shadow,background-color] duration-300"
>
    <div class="container-page flex h-16 lg:h-20 items-center justify-between gap-4">
        <a href="/" wire:navigate class="flex items-center gap-2 shrink-0" aria-label="{{ ($header['logo_text'] ?? 'Shubham International') }} home">
            <x-ui.logo size="md" :logo-text="$settings?->logo_text ?? 'S'" :label="$header['logo_text'] ?? 'Shubham International'" />
        </a>

        <nav class="hidden lg:flex items-center gap-1" aria-label="Primary">
            @foreach($menus as $item)
                @if($item->children && $item->children->count() > 0)
                    @php $isMega = $item->type === 'mega'; @endphp
                    <div
                        x-data="{ open: false }"
                        @mouseenter="open = true"
                        @mouseleave="open = false"
                        class="relative"
                    >
                        <button
                            class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-foreground/80 hover:text-primary transition-colors"
                            :aria-expanded="open"
                            aria-haspopup="menu"
                        >
                            {{ $item->title }}
                            <svg class="h-3.5 w-3.5 transition-transform" :class="open && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div
                            x-show="open"
                            x-transition
                            x-cloak
                            class="absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50"
                            @click.outside="open = false"
                            role="menu"
                        >
                            <div class="rounded-xl bg-popover hairline shadow-elevated p-5 {{ $isMega ? 'w-[640px]' : 'w-64' }} animate-fade-up">
                                <div class="{{ $isMega ? 'grid grid-cols-2 gap-x-6 gap-y-3' : 'space-y-3' }}">
                                    @foreach($item->children as $child)
                                        @if($child->type === 'external' && $child->url)
                                            <a href="{{ $child->url }}" target="_blank" rel="noreferrer" class="block py-2 text-sm text-foreground/80 hover:text-primary transition-colors">{{ $child->title }}</a>
                                        @else
                                            <a href="{{ $child->url ?? '/' }}" wire:navigate class="block group">
                                                <div class="font-medium text-sm text-foreground group-hover:text-primary transition-colors">{{ $child->title }}</div>
                                                @if($child->description)
                                                    <div class="text-xs text-muted-foreground mt-0.5">{{ $child->description }}</div>
                                                @endif
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $item->url ?? '/' }}" wire:navigate class="px-3 py-2 text-sm font-medium text-foreground/80 hover:text-primary transition-colors">{{ $item->title }}</a>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="tel:{{ $settings?->primary_phone ?? '18001234567' }}" class="hidden xl:flex items-center gap-2 pl-1 pr-2 py-1 rounded-md hover:bg-muted transition-colors" aria-label="Call us">
                <span class="size-9 rounded-full bg-secondary-soft text-secondary grid place-items-center">
                    @svg('lucide-phone', 'h-4 w-4')
                </span>
                <span class="leading-tight">
                    <span class="block text-[10px] font-medium uppercase tracking-wide text-muted-foreground">Call us</span>
                    <span class="block text-sm font-semibold">{{ $settings?->primary_phone ?? '+977-1-4234567' }}</span>
                </span>
            </a>
            <a href="{{ route('doctors.index') }}" wire:navigate class="hidden md:inline-flex items-center gap-1.5 rounded-md bg-muted px-3 py-2 text-sm font-medium text-foreground hover:bg-accent transition-colors">
                @svg('lucide-search', 'h-4 w-4') {{ $header['find_doctor_label'] ?? 'Find Doctor' }}
            </a>
            <a href="{{ route('appointment') }}" wire:navigate class="btn-lift hidden sm:inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground shadow-card hover:bg-primary/90">{{ $header['book_appointment_label'] ?? 'Book Appointment' }} @svg('lucide-arrow-right', 'h-4 w-4')</a>
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-foreground hover:bg-muted transition-colors min-h-11 min-w-11"
                aria-label="Open menu"
                :aria-expanded="mobileOpen"
                @click="mobileOpen = true"
            >
                @svg('lucide-menu', 'h-5 w-5')
            </button>
        </div>
    </div>

    {{-- Full-screen mobile menu portaled to body — only renders when opened (matches React) --}}
    <template x-teleport="body">
        <template x-if="mobileOpen">
            <div class="lg:hidden fixed inset-0 z-[100] bg-background flex flex-col animate-fade-up">
            <div class="flex items-center justify-between px-4 sm:px-6 h-16 border-b border-border shrink-0">
                <a href="/" wire:navigate class="flex items-center gap-2" aria-label="{{ ($header['logo_text'] ?? 'Shubham International') }} home">
                    <x-ui.logo size="md" :logo-text="$settings?->logo_text ?? 'S'" :label="$header['logo_text'] ?? 'Shubham International'" />
                </a>
                <button type="button" class="rounded-md p-2 hover:bg-muted min-h-11 min-w-11 grid place-items-center" aria-label="Close menu" @click="closeMenu()">
                    @svg('lucide-x', 'h-6 w-6')
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 divide-y divide-border" aria-label="Mobile primary">
                @foreach($menus as $item)
                    @if($item->children && $item->children->count() > 0)
                        <div>
                            <button
                                type="button"
                                class="w-full flex items-center justify-between py-4 text-base font-semibold text-foreground"
                                @click="toggleSection({{ $item->id }})"
                                :aria-expanded="isOpen({{ $item->id }})"
                            >
                                <span>{{ $item->title }}</span>
                                <svg class="h-5 w-5 text-muted-foreground transition-transform shrink-0" :class="isOpen({{ $item->id }}) && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div
                                class="grid transition-[grid-template-rows] duration-300 ease-out"
                                :class="isOpen({{ $item->id }}) ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
                            >
                                <div class="overflow-hidden">
                                    <div class="pb-4 pl-3 space-y-0 border-l-2 border-primary/20 ml-1">
                                        @foreach($item->children as $child)
                                            <a href="{{ $child->url ?? '/' }}" wire:navigate @click="closeMenu()" class="block pl-4 py-2.5 text-sm text-foreground/80 hover:text-primary transition-colors">
                                                <div class="font-medium">{{ $child->title }}</div>
                                                @if($child->description)
                                                    <div class="text-xs text-muted-foreground mt-0.5">{{ $child->description }}</div>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url ?? '/' }}" wire:navigate @click="closeMenu()" class="flex items-center justify-between py-4 text-base font-semibold text-foreground hover:text-primary transition-colors">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            <div class="px-4 sm:px-6 py-4 border-t border-border bg-surface shrink-0 grid grid-cols-2 gap-3">
                <a href="{{ route('appointment') }}" wire:navigate @click="closeMenu()" class="inline-flex items-center justify-center rounded-md bg-primary py-3 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity">{{ $header['book_appointment_label'] ?? 'Book Appointment' }}</a>
                <a href="tel:{{ $settings?->emergency_phone ?? '18001234567' }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-emergency py-3 text-sm font-semibold text-emergency-foreground hover:opacity-90 transition-opacity">
                    @svg('lucide-phone', 'h-4 w-4') Emergency
                </a>
            </div>
        </div>
        </template>
    </template>
</header>
