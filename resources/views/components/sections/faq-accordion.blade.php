@props([
    'faqs' => [],
    'eyebrow' => 'Patient FAQs',
    'title' => 'Answers to the questions we hear most.',
    'subtitle' => null,
])

@if(!empty($faqs))
    <section class="container-page section-y" x-data="{ open: 0 }">
        <div class="grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-4">
                <p class="text-eyebrow mb-3">{{ $eyebrow }}</p>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-balance">{{ $title }}</h2>
                @if($subtitle)
                    <p class="mt-4 text-muted-foreground leading-relaxed">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="lg:col-span-8 divide-y divide-border">
                @foreach($faqs as $index => $faq)
                    @php
                        $question = is_array($faq) ? ($faq['question'] ?? $faq['title'] ?? '') : (method_exists($faq, 'question') ? $faq->question : (string) $faq);
                        $answer = is_array($faq) ? ($faq['answer'] ?? '') : (method_exists($faq, 'answer') ? $faq->answer : '');
                    @endphp
                    <div class="py-2">
                        <button type="button" class="w-full flex items-center justify-between gap-4 py-4 text-left" @click="open = open === {{ $index }} ? null : {{ $index }}" :aria-expanded="open === {{ $index }}">
                            <span class="font-semibold pr-4">{{ $question }}</span>
                            <template x-if="open === {{ $index }}">
                                @svg('lucide-minus', 'h-5 w-5 shrink-0 text-primary')
                            </template>
                            <template x-if="open !== {{ $index }}">
                                @svg('lucide-plus', 'h-5 w-5 shrink-0 text-muted-foreground')
                            </template>
                        </button>
                        <div x-show="open === {{ $index }}" x-collapse>
                            <p class="pb-5 text-sm text-muted-foreground leading-relaxed pr-10">{{ $answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
