@props([
    'title' => 'Need Medical Assistance Today?',
    'subtitle' => null,
    'phone' => null,
    'bookLabel' => 'Book an Appointment Now',
    'bookUrl' => null,
    'contactLabel' => 'Contact us',
    'contactUrl' => null,
    'replyNote' => 'We usually reply within 5 minutes',
])

<section class="container-page pb-20">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0E62B4] via-primary to-[#1890DB] text-primary-foreground p-8 md:p-14 shadow-elevated">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true" style="background-image: radial-gradient(rgba(255,255,255,0.12) 1.5px, transparent 1.5px); background-size: 30px 30px;"></div>
        <div class="relative grid lg:grid-cols-2 gap-8 items-center">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-balance">{{ $title }}</h2>
                @if($subtitle)
                    <p class="mt-3 text-primary-foreground/85 max-w-xl leading-relaxed">{{ $subtitle }}</p>
                @endif
                @if($phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $phone) }}" class="mt-6 inline-flex items-center gap-3 rounded-full bg-white/15 px-5 py-2.5 font-semibold hover:bg-white/25 transition-colors">
                        <span class="cta-ring size-9 rounded-full bg-white/20 grid place-items-center" aria-hidden="true">@svg('lucide-phone', 'h-4 w-4')</span>
                        {{ $phone }}
                    </a>
                @endif
            </div>
            <div class="flex flex-col items-center lg:items-end gap-4">
                @if($bookUrl)
                    <a href="{{ $bookUrl }}" wire:navigate class="btn-lift inline-flex items-center gap-2 rounded-md bg-surface px-7 py-3.5 text-sm font-semibold text-foreground shadow-lg hover:bg-background">
                        {{ $bookLabel }} @svg('lucide-arrow-right', 'h-4 w-4')
                    </a>
                @endif
                <span class="inline-flex items-center gap-2 text-xs text-primary-foreground/80">
                    <span class="emg-pulse" aria-hidden="true"></span> {{ $replyNote }}
                </span>
                @if($contactUrl)
                    <a href="{{ $contactUrl }}" wire:navigate class="text-sm font-semibold text-primary-foreground/90 underline underline-offset-2 hover:text-white transition-colors">
                        {{ $contactLabel }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
