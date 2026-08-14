@props([])

@php
    $setting = \App\Models\SiteSetting::first();
    $theme = $setting?->theme ?? [];
@endphp

@if(!empty($theme))
<style>
    /* Tailwind v4 compiles utilities like `.bg-primary { background-color: var(--color-primary) }`,
       so runtime theme overrides MUST target the `--color-*` variables (not `--primary`).
       This style block is un-layered, so it beats the compiled `@layer theme` defaults. */
    :root {
    @php
        $pairs = [
            'primary'              => '--color-primary',
            'primary_foreground'   => '--color-primary-foreground',
            'primary_soft'         => '--color-primary-soft',
            'secondary'            => '--color-secondary',
            'secondary_foreground' => '--color-secondary-foreground',
            'secondary_soft'       => '--color-secondary-soft',
            'accent'               => '--color-accent',
            'accent_foreground'    => '--color-accent-foreground',
            'emergency'            => '--color-emergency',
            'emergency_foreground' => '--color-emergency-foreground',
            'emergency_soft'       => '--color-emergency-soft',
            'success'              => '--color-success',
            'success_soft'         => '--color-success-soft',
            'destructive'          => '--color-destructive',
            'destructive_foreground' => '--color-destructive-foreground',
            'background'           => '--color-background',
            'foreground'           => '--color-foreground',
            'muted'                => '--color-muted',
            'muted_foreground'     => '--color-muted-foreground',
            'border'               => '--color-border',
            'input'                => '--color-input',
            'ring'                 => '--color-ring',
            'surface'              => '--color-surface',
            'surface_muted'        => '--color-surface-muted',
            'chart_1'              => '--color-chart-1',
            'chart_2'              => '--color-chart-2',
            'chart_3'              => '--color-chart-3',
            'chart_4'              => '--color-chart-4',
            'chart_5'              => '--color-chart-5',
        ];

        // When only the main color is given, derive the -soft wash automatically.
        $derivedSoft = [
            'primary'   => 'primary_soft',
            'secondary' => 'secondary_soft',
            'accent'    => 'accent_soft',
            'emergency' => 'emergency_soft',
            'success'   => 'success_soft',
        ];

    @endphp
    @foreach($pairs as $key => $var)
        @if(!empty($theme[$key]))
            {{ $var }}: {{ $theme[$key] }};
        @endif
    @endforeach
    @foreach($derivedSoft as $main => $soft)
        @if(!empty($theme[$main]) && empty($theme[$soft]))
            --color-{{ str_replace('_', '-', $soft) }}: color-mix(in oklab, {{ $theme[$main] }} 8%, white);
        @endif
    @endforeach
    }
</style>
@endif
