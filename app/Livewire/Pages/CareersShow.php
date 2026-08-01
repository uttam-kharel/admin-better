<?php

namespace App\Livewire\Pages;

use App\Models\JobApplication;
use App\Models\JobOpening;
use Livewire\Component;
use Livewire\WithFileUploads;

class CareersShow extends Component
{
    use WithFileUploads;

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
            // Upload to Vercel Blob when BLOB_READ_WRITE_TOKEN is set; otherwise
            // embed a base64 data URI so it persists in the committed DB.
            $data['resume_url'] = \App\Services\BlobStorage::store($this->resume, 'cvs');
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

        return view('pages.careers.show', [
            'job' => $job,
            'related' => $related,
            'categoryLabel' => $categories[$job->category] ?? $job->category,
        ]);
    }
}
