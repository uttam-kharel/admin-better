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
    class="sticky top-0 z-40 bg-background/90 backdrop-blur-md border-b border-border"
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
            <a href="{{ route('doctors.index') }}" wire:navigate class="hidden md:inline-flex items-center gap-1.5 rounded-md bg-muted px-3 py-2 text-sm font-medium text-foreground hover:bg-accent transition-colors">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg> {{ $header['find_doctor_label'] ?? 'Find Doctor' }}
            </a>
            <a href="{{ route('appointment') }}" wire:navigate class="hidden sm:inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity">{{ $header['book_appointment_label'] ?? 'Book Appointment' }}</a>
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-foreground hover:bg-muted transition-colors min-h-11 min-w-11"
                aria-label="Open menu"
                :aria-expanded="mobileOpen"
                @click="mobileOpen = true"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
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
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> Emergency
                </a>
            </div>
        </div>
        </template>
    </template>
</header>
