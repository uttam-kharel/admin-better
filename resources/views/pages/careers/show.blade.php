<?php

use App\Models\JobOpening;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\JobApplication;


new class extends Component
{
public string $slug;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $cover_letter = '';
    public $resume = null;
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|max:20',
            'cover_letter' => 'nullable|max:5000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];
    }

    protected $messages = [
        'resume.mimes' => 'CV must be a PDF or Word document.',
        'resume.max' => 'CV must not exceed 10MB.',
    ];

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function submit(): void
    {
        $this->validate();

        $job = JobOpening::where('slug', $this->slug)->firstOrFail();

        $data = [
            'job_opening_id' => $job->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cover_letter' => $this->cover_letter,
        ];

        if ($this->resume) {
            $data['resume_url'] = $this->resume->store('cvs', 'public');
        }

        JobApplication::create($data);

        $this->submitted = true;
        $this->reset('resume');
        session()->flash('applied', true);
    }

    public function render()
    {
        $job = JobOpening::where('slug', $this->slug)->firstOrFail();
        $related = JobOpening::available()
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('department', $job->department)
                  ->orWhere('category', $job->category);
            })
            ->take(3)
            ->get();

        $categories = [
            'clinical' => 'Clinical',
            'allied-health' => 'Allied Health',
            'administration' => 'Administration',
            'technical' => 'IT & Technical',
            'support' => 'Facilities & Support',
        ];

        return $this->view([
            'job' => $job,
            'related' => $related,
            'categoryLabel' => $categories[$job->category] ?? $job->category,
        ]);
    }
};

?>
<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-10 md:py-14">
            <x-navigation.back-link :href="route('careers')">All openings</x-navigation.back-link>
            <div class="max-w-3xl">
                <p class="text-eyebrow mb-2">{{ $categoryLabel }} &middot; {{ ucwords(str_replace('-', ' ', $job->type)) }}</p>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $job->title }}</h1>
                <div class="flex flex-wrap gap-x-6 gap-y-2 mt-4 text-sm text-muted-foreground">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $job->location }}
                    </span>
                    @if($job->department)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ $job->department }}
                        </span>
                    @endif
                    @if($job->salary_range)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            {{ $job->salary_range }}
                        </span>
                    @endif
                    @if($job->closing_date)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            Closes {{ $job->closing_date->format('M j, Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container-page py-12">
        <div class="grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h2 class="text-lg font-semibold flex items-center gap-2 mb-3">
                        <svg class="h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        Description
                    </h2>
                    <p class="text-sm text-muted-foreground leading-relaxed">{{ $job->description }}</p>
                </div>

                @if($job->requirements)
                    <div>
                        <h2 class="text-lg font-semibold flex items-center gap-2 mb-3">
                            <svg class="h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Requirements
                        </h2>
                        <div class="text-sm text-muted-foreground leading-relaxed">{!! nl2br(e($job->requirements)) !!}</div>
                    </div>
                @endif

                @if($job->benefits)
                    <div>
                        <h2 class="text-lg font-semibold flex items-center gap-2 mb-3">
                            <svg class="h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
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
                                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
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
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium text-foreground" x-text="$wire.resume ? $wire.resume : 'Choose a file...'">Choose a file...</span>
                                        <p class="text-xs text-muted-foreground truncate" x-show="$wire.resume" x-text="$wire.resume"></p>
                                    </div>
                                    <input type="file" wire:model="resume" accept=".pdf,.doc,.docx" class="hidden" />
                                </label>
                                <template x-if="$wire.resume">
                                    <button type="button" wire:click="$set('resume', null)" class="text-xs text-emergency hover:underline mt-1.5 inline-flex items-center gap-1">
                                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        Remove file
                                    </button>
                                </template>
                                @error('resume') <p class="text-xs text-emergency mt-1.5">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="resume" class="flex items-center gap-2 text-xs text-primary mt-1.5">
                                    <svg class="h-3.5 w-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                    Uploading...
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-foreground/80 mb-1.5">Cover letter / Message</label>
                                <textarea wire:model="cover_letter" rows="4" class="w-full px-3 py-2.5 text-sm rounded-lg bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Tell us why you're a great fit for this role..."></textarea>
                            </div>
                            <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity disabled:opacity-60">
                                <span wire:loading.remove wire:target="submit">
                                    <svg class="h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
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
