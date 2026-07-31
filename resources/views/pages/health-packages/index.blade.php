<?php

use Livewire\Component;
use App\Models\HealthPackage;


new class extends Component
{
public function render()
    {
        $packages = HealthPackage::all();
        return $this->view(['packages' => $packages]);
    }
};

?>
<div>
    <x-sections.page-hero eyebrow="Health Packages" title="Preventive care, made simple" />

    <section class="container-page py-12 grid md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($packages as $pkg)
            <div class="relative rounded-2xl bg-surface p-7 flex flex-col {{ $pkg->is_popular ? 'hairline ring-2 ring-primary' : 'hairline' }}">
                @if($pkg->is_popular)
                    <div class="absolute top-0 right-6 -translate-y-1/2 bg-primary text-primary-foreground text-[10px] tracking-widest uppercase font-bold px-3 py-1 rounded">Popular</div>
                @endif
                <p class="text-eyebrow text-secondary">{{ $pkg->tier }}</p>
                <h3 class="text-lg font-semibold mt-2">{{ $pkg->name }}</h3>
                <p class="text-sm text-muted-foreground mt-2 leading-relaxed flex-1">{{ $pkg->description }}</p>
                <p class="mt-5 text-3xl font-bold">
                    ${{ number_format($pkg->price, 0) }}
                    <span class="text-sm font-normal text-muted-foreground ml-1">/ package</span>
                    @if($pkg->original_price)
                        <span class="ml-2 text-lg text-muted-foreground line-through">${{ number_format($pkg->original_price, 0) }}</span>
                    @endif
                </p>
                <ul class="mt-5 space-y-2 text-sm">
                    @foreach($pkg->inclusions ?? [] as $inc)
                        <li class="flex gap-2">
                            @svg('lucide-check-circle', 'h-4 w-4 text-secondary shrink-0 mt-0.5')
                            {{ $inc }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('appointment') }}" class="mt-6 inline-flex items-center justify-center rounded-md py-2.5 text-sm font-semibold transition-colors {{ $pkg->is_popular ? 'bg-primary text-primary-foreground hover:opacity-90' : 'bg-muted text-foreground hover:bg-accent' }}">
                    Book this package
                </a>
            </div>
        @endforeach
    </section>
</div>
