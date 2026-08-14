@props(['class' => ''])

{{-- Signature motif: a vital-signs (ECG) trace in the hospital's own
     instrument language. Draws itself once on load; the only animated
     decoration on the page. Pure CSS — honours prefers-reduced-motion. --}}
<svg {{ $attributes->merge(['class' => 'vitals-line ' . $class]) }} viewBox="0 0 480 44" fill="none" aria-hidden="true" focusable="false">
    <path d="M0 22 H188 L198 25 L209 8 L219 38 L229 21 H480" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
</svg>
