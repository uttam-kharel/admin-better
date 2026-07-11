<?php

use Livewire\Component;
use App\Models\Service;


new class extends Component
{
public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $service = Service::where('slug', $this->slug)->firstOrFail();
        $related = Service::where('id', '!=', $service->id)->take(3)->get();
        return $this->view(['service' => $service, 'related' => $related]);
    }
};

?>
<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-12">
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary mb-6">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                All services
            </a>
            <div class="flex items-center gap-3 mb-3">
                <div class="size-11 rounded-xl bg-primary text-primary-foreground grid place-items-center">
                    @svg('lucide-' . ($lucideMap[$service->icon ?? ''] ?? $service->icon ?? 'stethoscope'), 'h-5 w-5')
                </div>
                <p class="text-eyebrow">{{ $service->summary }}</p>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $service->name }}</h1>
            <p class="mt-4 text-lg text-muted-foreground max-w-3xl leading-relaxed">{{ $service->description }}</p>
        </div>
    </section>
    <section class="container-page py-12">
        <h2 class="text-2xl font-bold mb-6">Related services</h2>
        <div class="grid sm:grid-cols-3 gap-5">
            @foreach($related as $rel)
                <x-sections.related-service
                    :name="$rel->name"
                    :href="route('services.show', $rel->slug)"
                    :summary="$rel->summary"
                />
            @endforeach
        </div>
    </section>
</div>
