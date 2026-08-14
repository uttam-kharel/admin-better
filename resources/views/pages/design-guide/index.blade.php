<?php

use Livewire\Component;

new class extends Component
{
    public function render()
    {
        return $this->view()
            ->layout('layouts.public', ['title' => 'Design Guide — Shubham International Hospital']);
    }
};

?>

<div class="py-10 md:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-12">

        {{-- Page header --}}
        <header class="space-y-2">
            <p class="text-eyebrow">Shubham International</p>
            <h1 class="text-xl font-bold tracking-tight">Design Guide</h1>
            <p class="text-sm text-muted-foreground max-w-2xl">Living showcase of every reusable component, token, and pattern used across the admin and public site. Styles come from the same CSS variables the app uses, so overrides from Site Settings → Theme Colors are reflected here automatically.</p>
        </header>

        {{-- ============ Colors ============ --}}
        <section aria-labelledby="colors-heading">
            <h2 id="colors-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Colors · semantic tokens</h2>
            @php
                $tokens = [
                    ['primary', 'Primary actions, emphasis'],
                    ['primary-foreground', 'Text on primary'],
                    ['primary-soft', 'Primary washes'],
                    ['secondary', 'Secondary surfaces'],
                    ['secondary-foreground', 'Text on secondary'],
                    ['secondary-soft', 'Secondary washes'],
                    ['accent', 'Hover, active nav'],
                    ['emergency', 'Emergency, destructive'],
                    ['emergency-soft', 'Emergency washes'],
                    ['success', 'Success states'],
                    ['success-soft', 'Success washes'],
                    ['warning', 'Pending, idle'],
                    ['warning-soft', 'Warning washes'],
                    ['info', 'Responded, reviewed, informational'],
                    ['info-soft', 'Info washes'],
                    ['cyan', 'Running'],
                    ['cyan-soft', 'Cyan washes'],
                    ['orange', 'Paused'],
                    ['orange-soft', 'Orange washes'],
                    ['indigo', 'In progress'],
                    ['indigo-soft', 'Indigo washes'],
                    ['violet', 'In review, interviewed'],
                    ['violet-soft', 'Violet washes'],
                    ['rating', 'Star ratings'],
                    ['primary-deep', 'Gradient deep end'],
                    ['primary-light', 'Gradient light end'],
                    ['muted', 'Subdued surfaces'],
                    ['muted-foreground', 'Secondary text'],
                    ['background', 'Page background'],
                    ['foreground', 'Primary text'],
                    ['surface', 'Card surface'],
                    ['border', 'All borders'],
                    ['ring', 'Focus rings'],
                    ['chart-1', 'Chart series 1'],
                    ['chart-2', 'Chart series 2'],
                    ['chart-3', 'Chart series 3'],
                    ['chart-4', 'Chart series 4'],
                    ['chart-5', 'Chart series 5'],
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($tokens as [$token, $usage])
                    <div class="rounded-xl border border-border bg-surface overflow-hidden">
                        <div class="h-14" style="background: var(--color-{{ $token }})"></div>
                        <div class="p-3 space-y-0.5">
                            <p class="text-xs font-mono font-semibold">--color-{{ $token }}</p>
                            <p class="text-xs text-muted-foreground truncate" x-data x-init="$el.textContent = getComputedStyle(document.documentElement).getPropertyValue('--color-{{ $token }}').trim() || '—'">…</p>
                            <p class="text-[11px] text-muted-foreground/70">{{ $usage }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ Typography ============ --}}
        <section aria-labelledby="type-heading">
            <h2 id="type-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Typography · scale</h2>
            <div class="rounded-xl border border-border bg-surface divide-y divide-border">
                @php
                    $type = [
                        ['Display (serif)', 'font-display text-3xl font-bold tracking-tight', 'Shubham International Hospital'],
                        ['Page title', 'text-xl font-bold', 'Dashboard'],
                        ['Section title', 'text-lg font-semibold', 'Recent appointments'],
                        ['Section heading', 'text-sm font-semibold text-muted-foreground uppercase tracking-wide', 'Operations'],
                        ['Card title', 'text-sm font-semibold', 'Dr. Anisha Sharma'],
                        ['Body', 'text-sm', 'Internationally trained physicians with deep specialty experience.'],
                        ['Muted', 'text-sm text-muted-foreground', 'Description and secondary text.'],
                        ['Tiny label', 'text-xs text-muted-foreground', 'Mon, Aug 14 · 9:30 AM'],
                        ['Mono identifier', 'text-xs font-mono text-muted-foreground', 'APT-482913'],
                        ['Large stat', 'text-2xl font-bold tabular-nums', '1,248'],
                    ];
                @endphp
                @foreach($type as [$label, $classes, $example])
                    <div class="flex items-baseline justify-between gap-6 px-5 py-3.5">
                        <p class="{{ $classes }} min-w-0">{{ $example }}</p>
                        <p class="text-xs text-muted-foreground font-mono shrink-0">{{ $classes }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ Status badges ============ --}}
        <section aria-labelledby="badges-heading">
            <h2 id="badges-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Status badges · variants</h2>
            <div class="rounded-xl border border-border bg-surface p-5 flex flex-wrap gap-2.5">
                @php
                    $badges = [
                        ['primary', 'new / todo'],
                        ['success', 'confirmed / active / hired'],
                        ['warning', 'pending / idle'],
                        ['info', 'responded / reviewed'],
                        ['violet', 'interviewed / in_review'],
                        ['indigo', 'in_progress'],
                        ['cyan', 'running'],
                        ['orange', 'paused'],
                        ['danger', 'rejected / failed'],
                        ['neutral', 'archived / inactive'],
                    ];
                @endphp
                @foreach($badges as [$variant, $label])
                    <x-ui.badge :variant="$variant" dot>{{ $label }}</x-ui.badge>
                @endforeach
            </div>
        </section>

        {{-- ============ Signature ============ --}}
        <section aria-labelledby="signature-heading">
            <h2 id="signature-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Signature · vital-signs line</h2>
            <div class="rounded-xl border border-border bg-surface p-5">
                <x-ui.vitals-line />
                <p class="mt-3 text-xs text-muted-foreground max-w-md">The one animated motif on the site — an ECG trace that draws itself under the hero headline. Drawn from the hospital's own instrument language; honours <code class="font-mono">prefers-reduced-motion</code>.</p>
            </div>
        </section>

        {{-- ============ Buttons ============ --}}
        <section aria-labelledby="buttons-heading">
            <h2 id="buttons-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Buttons · variants &amp; sizes</h2>
            <div class="space-y-4">
                <div class="rounded-xl border border-border bg-surface p-5 flex flex-wrap items-center gap-3">
                    @foreach(['primary', 'secondary', 'outline', 'ghost', 'destructive', 'emergency'] as $variant)
                        <x-ui.button :variant="$variant">@svg('lucide-plus', 'h-4 w-4') {{ ucfirst($variant) }}</x-ui.button>
                    @endforeach
                </div>
                <div class="rounded-xl border border-border bg-surface p-5 flex flex-wrap items-center gap-3">
                    @foreach(['sm', 'md', 'lg'] as $size)
                        <x-ui.button :size="$size">{{ $size === 'sm' ? 'Small' : ($size === 'md' ? 'Medium' : 'Large') }}</x-ui.button>
                    @endforeach
                    <x-ui.button disabled>Disabled</x-ui.button>
                    <x-ui.button href="/" variant="outline">Link button</x-ui.button>
                </div>
            </div>
        </section>

        {{-- ============ Cards ============ --}}
        <section aria-labelledby="cards-heading">
            <h2 id="cards-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Cards · variants</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach(['default' => 'Default', 'soft' => 'Soft', 'flat' => 'Flat', 'muted' => 'Muted'] as $variant => $label)
                    <x-ui.card :variant="$variant">
                        <p class="text-sm font-semibold">{{ $label }}</p>
                        <p class="text-sm text-muted-foreground mt-1">Card bodies sit on `surface` with a hairline border and `rounded-xl`.</p>
                    </x-ui.card>
                @endforeach
            </div>
        </section>

        {{-- ============ Metric cards ============ --}}
        <section aria-labelledby="metrics-heading">
            <h2 id="metrics-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Metric cards · pattern</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.stat-card :value="number_format(1248)" label="Total visits" :delta="12.4">
                    <x-slot:icon>@svg('lucide-eye')</x-slot:icon>
                </x-ui.stat-card>
                <x-ui.stat-card :value="number_format(86)" label="New leads" :delta="-3.1">
                    <x-slot:icon>@svg('lucide-users')</x-slot:icon>
                </x-ui.stat-card>
                <x-ui.stat-card :value="number_format(24)" label="Pending approvals" :delta="0">
                    <x-slot:icon>@svg('lucide-calendar-clock')</x-slot:icon>
                </x-ui.stat-card>
                <x-ui.stat-card href="/admin/doctors" :value="number_format(15)" label="Doctors">
                    <x-slot:icon>@svg('lucide-stethoscope')</x-slot:icon>
                </x-ui.stat-card>
            </div>
        </section>

        {{-- ============ Forms ============ --}}
        <section aria-labelledby="forms-heading">
            <h2 id="forms-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Forms · controls</h2>
            <div class="grid md:grid-cols-2 gap-5">
                <x-ui.card>
                    <p class="text-sm font-semibold mb-4">Inputs</p>
                    <div class="space-y-4">
                        <x-form.input label="Full name" type="text" placeholder="e.g. Anisha Sharma…" autocomplete="name" />
                        <x-form.input label="Email" type="email" placeholder="you@example.com…" autocomplete="email" spellcheck="false" />
                        <x-form.input label="Search" hint="Debounced search example." />
                        <x-form.textarea label="Message" rows="3" placeholder="Tell us what you'd like to discuss…" />
                    </div>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-sm font-semibold mb-4">Select</p>
                    <div class="space-y-4">
                        <x-form.select-menu
                            model="demoSelect"
                            :value="'cardiology'"
                            :options="[['value' => '', 'label' => 'All departments'], ['value' => 'cardiology', 'label' => 'Cardiology'], ['value' => 'neurology', 'label' => 'Neurology'], ['value' => 'orthopedics', 'label' => 'Orthopedics'], ['value' => 'pediatrics', 'label' => 'Pediatrics'], ['value' => 'radiology', 'label' => 'Radiology'], ['value' => 'emergency', 'label' => 'Emergency Medicine'], ['value' => 'oncology', 'label' => 'Oncology']]"
                            label="Department"
                            placeholder="All departments"
                        />
                        <x-form.search-input placeholder="Search records…" aria-label="Search" />
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" id="dg-check" checked class="rounded border-border accent-primary" />
                            <label for="dg-check" class="text-sm">Shared hit target (label + control)</label>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </section>

        {{-- ============ Pills / feedback ============ --}}
        <section aria-labelledby="pills-heading">
            <h2 id="pills-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Pills &amp; feedback</h2>
            <div class="grid md:grid-cols-2 gap-5">
                <x-ui.card>
                    <p class="text-sm font-semibold mb-4">Pills</p>
                    <div class="flex flex-wrap gap-2.5">
                        <x-ui.pill variant="bordered" :active="true">Active</x-ui.pill>
                        <x-ui.pill variant="bordered">Bordered</x-ui.pill>
                        <x-ui.pill variant="filled" :active="true">Filled</x-ui.pill>
                        <x-ui.pill variant="filled">Filled</x-ui.pill>
                        <x-ui.pill variant="tag">Tag</x-ui.pill>
                        <x-ui.pill variant="tag">#cardiology</x-ui.pill>
                    </div>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-sm font-semibold mb-4">Empty state</p>
                    <x-feedback.empty-state size="sm" title="No records found" description="Try adjusting your search, or click New to add your first record.">
                        <x-slot:icon>@svg('lucide-inbox', 'h-14 w-14')</x-slot:icon>
                    </x-feedback.empty-state>
                </x-ui.card>
            </div>
        </section>

        {{-- ============ Composition patterns ============ --}}
        <section aria-labelledby="patterns-heading">
            <h2 id="patterns-heading" class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-4">Composition · patterns</h2>
            <div class="grid md:grid-cols-2 gap-5">
                <x-ui.card>
                    <p class="text-sm font-semibold mb-4">Property rows</p>
                    <div class="divide-y divide-border">
                        @php
                            $propertyRows = [
                                ['Status', '<x-ui.badge variant="success" dot>Active</x-ui.badge>'],
                                ['Department', 'Cardiology'],
                                ['Experience', '12+ years'],
                                ['Languages', 'English · Nepali'],
                            ];
                        @endphp
                        @foreach($propertyRows as [$k, $v])
                            <div class="flex items-center justify-between py-2.5 gap-4">
                                <span class="text-xs text-muted-foreground">{{ $k }}</span>
                                <span class="text-sm font-medium text-right">{!! $v !!}</span>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
                <x-ui.card padding="none" class="overflow-hidden">
                    <p class="text-sm font-semibold px-5 pt-4 pb-3 border-b border-border">Grouped list</p>
                    <div class="px-5 py-2.5 bg-muted/50 flex items-center gap-2">
                        <x-ui.badge variant="primary" dot>Pending</x-ui.badge>
                        <span class="text-sm font-medium">Pending</span>
                        <span class="text-xs text-muted-foreground ml-auto">2</span>
                    </div>
                    <div class="divide-y divide-border">
                        <div class="flex items-center justify-between px-5 py-3 gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">Rajesh Kumar</p>
                                <p class="text-xs text-muted-foreground truncate">Cardiology · Aug 16</p>
                            </div>
                            <x-ui.badge variant="warning">pending</x-ui.badge>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3 gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">Sunita Gurung</p>
                                <p class="text-xs text-muted-foreground truncate">Neurology · Aug 17</p>
                            </div>
                            <x-ui.badge variant="warning">pending</x-ui.badge>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </section>
    </div>
</div>
