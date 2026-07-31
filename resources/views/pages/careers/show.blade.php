<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-10 md:py-14">
            <x-navigation.back-link :href="route('careers')">All openings</x-navigation.back-link>
            <div class="max-w-3xl">
                <p class="text-eyebrow mb-2">{{ $categoryLabel }} &middot; {{ ucwords(str_replace('-', ' ', $job->type)) }}</p>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $job->title }}</h1>
                <x-ui.meta-bar class="mt-4" :items="[
                    ['icon' => 'map-pin', 'value' => $job->location],
                    ['icon' => 'users', 'value' => $job->department],
                    ['icon' => 'dollar-sign', 'value' => $job->salary_range],
                    ['icon' => 'calendar', 'prefix' => 'Closes', 'value' => $job->closing_date ? $job->closing_date->format('M j, Y') : null],
                ]" />
            </div>
        </div>
    </section>

    <section class="container-page py-12">
        <div class="grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h2 class="text-lg font-semibold flex items-center gap-2 mb-3">
                        @svg('lucide-info', 'h-5 w-5 text-primary')
                        Description
                    </h2>
                    <p class="text-sm text-muted-foreground leading-relaxed">{{ $job->description }}</p>
                </div>

                @if($job->requirements)
                    <div>
                        <h2 class="text-lg font-semibold flex items-center gap-2 mb-3">
                            @svg('lucide-shield-check', 'h-5 w-5 text-primary')
                            Requirements
                        </h2>
                        <div class="text-sm text-muted-foreground leading-relaxed">{!! nl2br(e($job->requirements)) !!}</div>
                    </div>
                @endif

                @if($job->benefits)
                    <div>
                        <h2 class="text-lg font-semibold flex items-center gap-2 mb-3">
                            @svg('lucide-pencil', 'h-5 w-5 text-primary')
                            Benefits
                        </h2>
                        <div class="text-sm text-muted-foreground leading-relaxed">{!! nl2br(e($job->benefits)) !!}</div>
                    </div>
                @endif
            </div>

            <div>
                <div class="rounded-2xl bg-surface hairline p-6 sticky top-28">
                    @if(session('applied'))
                        <div class="text-center py-6">
                            <div class="size-14 rounded-full bg-primary-soft text-primary grid place-items-center mx-auto mb-4">
                                @svg('lucide-badge-check', 'h-7 w-7')
                            </div>
                            <h3 class="font-semibold text-lg">Application submitted!</h3>
                            <p class="text-sm text-muted-foreground mt-2">We'll review your application and get back to you soon.</p>
                        </div>
                    @else
                        <h3 class="font-semibold text-lg mb-1">Apply for this position</h3>
                        <p class="text-xs text-muted-foreground mb-5">Fill out the form below and we'll be in touch.</p>
                        <form wire:submit="submit" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-foreground/80 mb-1.5">Full name <span class="text-emergency">*</span></label>
                                <input type="text" wire:model="name" class="w-full px-3 py-2.5 text-sm rounded-lg bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="John Doe" />
                                @error('name') <p class="text-xs text-emergency mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-foreground/80 mb-1.5">Email <span class="text-emergency">*</span></label>
                                <input type="email" wire:model="email" class="w-full px-3 py-2.5 text-sm rounded-lg bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="john@example.com" />
                                @error('email') <p class="text-xs text-emergency mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-foreground/80 mb-1.5">Phone</label>
                                <input type="tel" wire:model="phone" class="w-full px-3 py-2.5 text-sm rounded-lg bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="+977-98..." />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-foreground/80 mb-1.5">Upload CV <span class="text-muted-foreground font-normal">(PDF or Word, up to 10MB)</span></label>
                                <label class="flex items-center gap-3 px-4 py-3 rounded-lg border-2 border-dashed border-border bg-background hover:bg-muted/50 hover:border-primary/40 cursor-pointer transition-colors">
                                    <div class="size-10 rounded-lg bg-primary-soft text-primary grid place-items-center shrink-0">
                                        @svg('lucide-download', 'h-5 w-5')
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium text-foreground" x-text="$wire.resume ? $wire.resume : 'Choose a file...'">Choose a file...</span>
                                        <p class="text-xs text-muted-foreground truncate" x-show="$wire.resume" x-text="$wire.resume"></p>
                                    </div>
                                    <input type="file" wire:model="resume" accept=".pdf,.doc,.docx" class="hidden" />
                                </label>
                                <template x-if="$wire.resume">
                                    <button type="button" wire:click="$set('resume', null)" class="text-xs text-emergency hover:underline mt-1.5 inline-flex items-center gap-1">
                                        @svg('lucide-x', 'h-3 w-3')
                                        Remove file
                                    </button>
                                </template>
                                @error('resume') <p class="text-xs text-emergency mt-1.5">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="resume" class="flex items-center gap-2 text-xs text-primary mt-1.5">
                                    @svg('lucide-refresh-cw', 'h-3.5 w-3.5 animate-spin')
                                    Uploading...
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-foreground/80 mb-1.5">Cover letter / Message</label>
                                <textarea wire:model="cover_letter" rows="4" class="w-full px-3 py-2.5 text-sm rounded-lg bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Tell us why you're a great fit for this role..."></textarea>
                            </div>
                            <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity disabled:opacity-60">
                                <span wire:loading.remove wire:target="submit">
                                    @svg('lucide-plus', 'h-4 w-4 inline mr-1')
                                    Submit Application
                                </span>
                                <span wire:loading wire:target="submit">Submitting...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($related->count() > 0)
        <section class="bg-gradient-to-b from-background to-primary-soft/30">
            <div class="container-page py-16">
                <h2 class="text-2xl font-bold mb-6">Similar openings</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($related as $rel)
                        <a href="{{ route('careers.show', $rel->slug) }}" class="rounded-2xl bg-surface hairline p-5 hover:shadow-card transition-shadow group">
                            <h3 class="font-semibold group-hover:text-primary transition-colors">{{ $rel->title }}</h3>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-muted-foreground mt-2">
                                <span>{{ $rel->location }}</span>
                                @if($rel->department)<span>&middot; {{ $rel->department }}</span>@endif
                                <span>&middot; <span class="capitalize">{{ str_replace('-', ' ', $rel->type) }}</span></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
