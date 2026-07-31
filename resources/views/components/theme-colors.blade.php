@props([])

@php
    $setting = \App\Models\SiteSetting::first();
    $theme = $setting?->theme ?? [];
@endphp

@if(!empty($theme))
<style>
:root {
    @if(!empty($theme['primary']))
        --primary: {{ $theme['primary'] }};
    @endif
    @if(!empty($theme['primary_foreground']))
        --primary-foreground: {{ $theme['primary_foreground'] }};
    @endif
    @if(!empty($theme['primary_soft']))
        --primary-soft: {{ $theme['primary_soft'] }};
    @endif
    @if(!empty($theme['secondary']))
        --secondary: {{ $theme['secondary'] }};
    @endif
    @if(!empty($theme['secondary_foreground']))
        --secondary-foreground: {{ $theme['secondary_foreground'] }};
    @endif
    @if(!empty($theme['secondary_soft']))
        --secondary-soft: {{ $theme['secondary_soft'] }};
    @endif
    @if(!empty($theme['emergency']))
        --emergency: {{ $theme['emergency'] }};
    @endif
    @if(!empty($theme['emergency_foreground']))
        --emergency-foreground: {{ $theme['emergency_foreground'] }};
    @endif
    @if(!empty($theme['emergency_soft']))
        --emergency-soft: {{ $theme['emergency_soft'] }};
    @endif
    @if(!empty($theme['accent']))
        --accent: {{ $theme['accent'] }};
    @endif
    @if(!empty($theme['accent_foreground']))
        --accent-foreground: {{ $theme['accent_foreground'] }};
    @endif
}
</style>
@endif
