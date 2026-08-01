<?php

use App\Models\Appointment;
use App\Models\Author;
use App\Models\Award;
use App\Models\BlogPost;
use App\Models\CmsPage;
use App\Models\ContactSubmission;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\HealthPackage;
use App\Models\HeroSlide;
use App\Models\InsurancePartner;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\MenuItem;
use App\Models\PatientStory;
use App\Models\QuickAction;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\AdminUser;


new class extends Component
{
public string $resource;
    public string $search = '';
    public bool $modalOpen = false;
    public bool $creating = false;
    public string|int|null $editingId = null;
    public array $form = [];
    public bool $showViewModal = false;
    public $viewItem = null;

    protected array $resourceMap = [
        'appointments' => Appointment::class,
        'contact-submissions' => ContactSubmission::class,
        'doctors' => Doctor::class,
        'departments' => Department::class,
        'services' => Service::class,
        'health-packages' => HealthPackage::class,
        'blogs' => BlogPost::class,
        'authors' => Author::class,
        'gallery' => GalleryItem::class,
        'hero-slides' => HeroSlide::class,
        'quick-actions' => QuickAction::class,
        'stats' => Stat::class,
        'testimonials' => Testimonial::class,
        'stories' => PatientStory::class,
        'treatments' => Treatment::class,
        'technologies' => Technology::class,
        'awards' => Award::class,
        'job-openings' => JobOpening::class,
        'job-applications' => JobApplication::class,
        'insurance' => InsurancePartner::class,
        'faqs' => Faq::class,
        'menus' => MenuItem::class,
        'pages' => CmsPage::class,
        'settings' => SiteSetting::class,
        'admin-users' => AdminUser::class,
    ];

    public function create(): void
    {
        $this->creating = true;
        $this->editingId = null;
        $this->form = $this->emptyForm();
        $this->modalOpen = true;
    }

    public function edit(string|int $id): void
    {
        $item = $this->query()->findOrFail($id);

        $this->creating = false;
        $this->editingId = $item->getKey();
        $this->form = $this->formFromModel($item);
        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->creating = false;
        $this->editingId = null;
        $this->form = [];
    }

    public function save(): void
    {
        $modelClass = $this->resolveModel();
        $data = $this->coercedFormData();

        if ($modelClass === AdminUser::class) {
            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } elseif (! $this->creating) {
                unset($data['password']);
            }
        }

        if ($this->creating) {
            if ($modelClass === Appointment::class && empty($data['id'])) {
                $data['id'] = 'APT-' . substr((string) time(), -6);
            }

            if ($modelClass === ContactSubmission::class && empty($data['id'])) {
                $data['id'] = 'MSG-' . substr((string) time(), -6);
            }

            $modelClass::create($data);
            session()->flash('message', 'Created successfully.');
        } else {
            $item = $this->query()->findOrFail($this->editingId);
            $item->update($data);
            session()->flash('message', 'Saved successfully.');
        }

        $this->closeModal();
    }

    public function delete(string|int $id): void
    {
        $this->query()->findOrFail($id)->delete();
        session()->flash('message', 'Deleted successfully.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // NOTE: named showDetails, not view() — Livewire auto-generates a view()
    // helper for anonymous Blade components, so a user-defined view() here
    // caused a PHP fatal 'Cannot redeclare' and 500s on every admin page.
    public function showDetails(string|int $id): void
    {
        $this->viewItem = $this->query()->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeView(): void
    {
        $this->showViewModal = false;
        $this->viewItem = null;
    }

    public function render()
    {
        $config = $this->resourceConfig();
        $query = $this->query();

        if ($this->search !== '') {
            $query->where(function ($q) use ($config) {
                foreach ($config['search'] as $index => $key) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $q->{$method}($key, 'like', "%{$this->search}%");
                }
            });
        }

        $total = $this->query()->count();
        $items = $query->latest()->paginate(20);

        return $this->view([
            'items' => $items,
            'total' => $total,
            'title' => $config['title'],
            'description' => $config['description'] ?? null,
            'columns' => $config['columns'],
            'fields' => $config['fields'],
            'viewable' => $config['viewable'] ?? true,
            'modalTitle' => ($this->creating ? 'New ' : 'Edit ') . rtrim($config['title'], 's'),
        ])->layout('layouts.admin', ['title' => $config['title'].' — Admin']);
    }

    protected function resolveModel(): string
    {
        if (! isset($this->resourceMap[$this->resource])) {
            abort(404);
        }

        return $this->resourceMap[$this->resource];
    }

    protected function query()
    {
        return $this->resolveModel()::query();
    }

    protected function emptyForm(): array
    {
        $form = [];

        foreach ($this->resourceConfig()['fields'] as $field) {
            $form[$field['name']] = match ($field['type']) {
                'number' => '',
                'tags' => '',
                'json' => $field['placeholder'] ?? "[]",
                default => $field['default'] ?? '',
            };
        }

        return $form;
    }

    protected function formFromModel(Model $item): array
    {
        $form = [];

        foreach ($this->resourceConfig()['fields'] as $field) {
            $value = data_get($item, $field['name']);
            $form[$field['name']] = match ($field['type']) {
                'tags' => is_array($value) ? implode(', ', $value) : (string) ($value ?? ''),
                'json' => json_encode($value ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'boolean' => $value ? '1' : '0',
                default => (string) ($value ?? ''),
            };
        }

        return $form;
    }

    protected function coercedFormData(): array
    {
        $data = [];

        foreach ($this->resourceConfig()['fields'] as $field) {
            $name = $field['name'];
            $value = $this->form[$name] ?? null;

            if ($field['type'] === 'image' && $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Upload to Vercel Blob when BLOB_READ_WRITE_TOKEN is set; otherwise
                // embed a base64 data URI (persists in the committed SQLite DB).
                $data[$name] = \App\Services\BlobStorage::store($value, 'images');
                continue;
            }

            $data[$name] = match ($field['type']) {
                'number' => $value === '' ? null : (float) $value,
                'tags' => collect(explode(',', (string) $value))->map(fn ($v) => trim($v))->filter()->values()->all(),
                'json' => $this->decodeJson($value),
                'boolean' => in_array($value, ['1', 1, true, 'true', 'yes'], true),
                default => $value === '' ? null : $value,
            };
        }

        return $data;
    }

    protected function decodeJson(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    protected function resourceConfig(): array
    {
        $configs = [
            'appointments' => [
                'title' => 'Appointments',
                'description' => 'Inbound appointment requests.',
                'search' => ['name', 'email', 'phone', 'department_slug', 'status'],
                'columns' => $this->columns([
                    'name' => 'Patient',
                    'department_slug' => 'Department',
                    'preferred_date' => 'Date',
                    'status' => 'Status',
                ]),
                'fields' => [
                    $this->field('name', 'Patient name', 'text', required: true),
                    $this->field('phone', 'Phone', 'text', required: true),
                    $this->field('email', 'Email', 'email', required: true),
                    $this->field('department_slug', 'Department slug', 'text', required: true),
                    $this->field('doctor_slug', 'Doctor slug'),
                    $this->field('preferred_date', 'Preferred date', 'date'),
                    $this->field('status', 'Status', 'select', required: true, options: $this->statusOptions(['pending', 'confirmed', 'completed', 'cancelled'])),
                    $this->field('message', 'Message', 'textarea'),
                ],
            ],
            'contact-submissions' => [
                'title' => 'Contact Inbox',
                'description' => 'Messages submitted via the public contact form.',
                'search' => ['name', 'email', 'subject', 'status'],
                'columns' => $this->columns(['name' => 'From', 'subject' => 'Subject', 'status' => 'Status']),
                'fields' => [
                    $this->field('name', 'Name', 'text', required: true),
                    $this->field('email', 'Email', 'email', required: true),
                    $this->field('phone', 'Phone'),
                    $this->field('subject', 'Subject'),
                    $this->field('status', 'Status', 'select', required: true, options: $this->statusOptions(['new', 'responded', 'archived'])),
                    $this->field('message', 'Message', 'textarea', required: true),
                ],
            ],
            'doctors' => [
                'title' => 'Doctors',
                'description' => 'Specialist profiles shown on the public site.',
                'search' => ['name', 'slug', 'designation', 'department'],
                'columns' => [['key' => 'photo', 'label' => 'Photo', 'type' => 'image_text'], ['key' => 'designation', 'label' => 'Designation'], ['key' => 'department', 'label' => 'Department'], ['key' => 'experience_years', 'label' => 'Experience']],
                'fields' => [
                    $this->field('name', 'Name', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('designation', 'Designation'),
                    $this->field('department', 'Department'),
                    $this->field('department_slug', 'Department slug'),
                    $this->field('experience_years', 'Experience years', 'number'),
                    $this->field('photo', 'Photo', 'image'),
                    $this->field('bio', 'Bio', 'textarea'),
                    $this->field('qualifications', 'Qualifications', 'tags'),
                    $this->field('languages', 'Languages', 'tags'),
                    $this->field('expertise', 'Expertise', 'tags'),
                    $this->field('schedule', 'Schedule JSON', 'json', placeholder: '[{"day":"Mon","hours":"9-1"}]'),
                    $this->field('publications', 'Publications', 'tags'),
                ],
            ],
            'departments' => [
                'title' => 'Departments',
                'description' => 'Centers of excellence and specialties.',
                'search' => ['name', 'slug', 'tagline'],
                'columns' => [['key' => 'image', 'label' => 'Department', 'type' => 'image_text'], ['key' => 'tagline', 'label' => 'Tagline'], ['key' => 'icon', 'label' => 'Icon'], ['key' => 'slug', 'label' => 'Slug']],
                'fields' => [
                    $this->field('name', 'Name', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('tagline', 'Tagline'),
                    $this->field('description', 'Description', 'textarea'),
                    $this->field('icon', 'Icon'),
                    $this->field('image', 'Image', 'image'),
                    $this->field('treatments', 'Treatments', 'tags'),
                    $this->field('facilities', 'Facilities', 'tags'),
                ],
            ],
            'services' => [
                'title' => 'Services',
                'search' => ['name', 'slug', 'summary', 'department_slug'],
                'columns' => $this->columns(['name' => 'Service', 'slug' => 'Slug', 'summary' => 'Summary', 'department_slug' => 'Department']),
                'fields' => [
                    $this->field('name', 'Service name', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('summary', 'Summary'),
                    $this->field('description', 'Description', 'textarea'),
                    $this->field('icon', 'Icon'),
                    $this->field('department_slug', 'Department slug'),
                ],
            ],
            'health-packages' => [
                'title' => 'Health Packages',
                'search' => ['name', 'slug', 'tier'],
                'columns' => $this->columns(['name' => 'Package name', 'slug' => 'Slug', 'tier' => 'Tier', 'price' => 'Discounted price']),
                'fields' => [
                    $this->field('name', 'Package name', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('tier', 'Tier', 'select', required: true, options: $this->optionList(['essential', 'comprehensive', 'specialized'])),
                    $this->field('price', 'Discounted price', 'number', required: true),
                    $this->field('original_price', 'Original price', 'number'),
                    $this->field('currency', 'Currency', default: 'USD'),
                    $this->field('description', 'Description', 'textarea'),
                    $this->field('inclusions', 'Inclusions', 'tags'),
                    $this->field('is_popular', 'Popular?', 'boolean'),
                ],
            ],
            'blogs' => [
                'title' => 'Blog Posts',
                'description' => 'Articles in the health library.',
                'search' => ['title', 'slug', 'category', 'author'],
                'columns' => $this->columns(['title' => 'Title', 'category' => 'Category', 'author' => 'Author', 'image' => 'Image']),
                'fields' => [
                    $this->field('title', 'Title', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('category', 'Category'),
                    $this->field('author', 'Author name'),
                    $this->field('author_id', 'Author ID', 'number'),
                    $this->field('read_minutes', 'Read time (minutes)', 'number'),
                    $this->field('published_at', 'Published date', 'date'),
                    $this->field('image', 'Image URL', 'url'),
                    $this->field('excerpt', 'Excerpt', 'textarea'),
                    $this->field('content', 'Content', 'textarea'),
                    $this->field('tags', 'Tags', 'tags'),
                ],
            ],
            'authors' => [
                'title' => 'Authors',
                'description' => 'Blog post authors.',
                'search' => ['name', 'slug', 'specialty'],
                'columns' => $this->columns(['name' => 'Name', 'slug' => 'Slug', 'specialty' => 'Specialty', 'photo' => 'Photo']),
                'fields' => [
                    $this->field('name', 'Full name', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('specialty', 'Specialty'),
                    $this->field('photo', 'Photo URL', 'url'),
                    $this->field('bio', 'Bio', 'textarea'),
                ],
            ],
            'gallery' => [
                'title' => 'Gallery',
                'description' => 'Manage images and videos.',
                'search' => ['title', 'category', 'type'],
                'columns' => $this->columns(['title' => 'Title', 'type' => 'Type', 'category' => 'Category', 'url' => 'URL']),
                'fields' => [
                    $this->field('title', 'Title', 'text', required: true),
                    $this->field('type', 'Type', 'select', required: true, options: $this->optionList(['photo', 'video'])),
                    $this->field('category', 'Category'),
                    $this->field('url', 'Media URL', 'url', required: true),
                    $this->field('thumbnail', 'Thumbnail URL', 'url'),
                ],
            ],
            'hero-slides' => [
                'title' => 'Hero Slides',
                'description' => 'The first slide is shown on the homepage hero.',
                'search' => ['title', 'eyebrow', 'cta_label'],
                'columns' => $this->columns(['title' => 'Title', 'eyebrow' => 'Eyebrow', 'cta_label' => 'CTA', 'order' => 'Order']),
                'fields' => [
                    $this->field('eyebrow', 'Eyebrow'),
                    $this->field('title', 'Title', 'text', required: true),
                    $this->field('subtitle', 'Subtitle', 'textarea'),
                    $this->field('image', 'Image URL', 'url'),
                    $this->field('cta_label', 'Primary CTA label'),
                    $this->field('cta_url', 'Primary CTA URL'),
                    $this->field('secondary_cta_label', 'Secondary CTA label'),
                    $this->field('secondary_cta_url', 'Secondary CTA URL'),
                    $this->field('order', 'Order', 'number'),
                ],
            ],
            'quick-actions' => [
                'title' => 'Quick Actions',
                'search' => ['label', 'helper', 'tone'],
                'columns' => $this->columns(['label' => 'Label', 'helper' => 'Helper', 'url' => 'URL', 'tone' => 'Tone']),
                'fields' => [
                    $this->field('label', 'Label', 'text', required: true),
                    $this->field('helper', 'Helper text'),
                    $this->field('url', 'URL', 'text', required: true),
                    $this->field('icon', 'Icon'),
                    $this->field('tone', 'Tone', 'select', required: true, options: $this->optionList(['emergency', 'primary', 'secondary', 'neutral'])),
                ],
            ],
            'stats' => [
                'title' => 'Stats',
                'search' => ['value', 'label'],
                'columns' => $this->columns(['value' => 'Value', 'label' => 'Label']),
                'fields' => [
                    $this->field('value', 'Value', 'text', required: true, helper: 'e.g. 1,200+'),
                    $this->field('label', 'Label', 'text', required: true),
                ],
            ],
            'testimonials' => [
                'title' => 'Testimonials',
                'search' => ['name', 'location', 'treatment', 'quote'],
                'columns' => $this->columns(['name' => 'Patient', 'location' => 'Location', 'rating' => 'Rating', 'treatment' => 'Treatment']),
                'fields' => [
                    $this->field('name', 'Patient name', 'text', required: true),
                    $this->field('location', 'Location'),
                    $this->field('rating', 'Rating (1-5)', 'number'),
                    $this->field('treatment', 'Treatment'),
                    $this->field('quote', 'Quote', 'textarea', required: true),
                    $this->field('photo', 'Photo URL', 'url'),
                ],
            ],
            'stories' => [
                'title' => 'Patient Stories',
                'search' => ['title', 'slug', 'patient'],
                'columns' => $this->columns(['title' => 'Title', 'slug' => 'Slug', 'patient' => 'Patient', 'url' => 'URL']),
                'fields' => [
                    $this->field('title', 'Story title', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('patient', 'Patient name'),
                    $this->field('image', 'Image URL', 'url'),
                    $this->field('url', 'Read more URL'),
                    $this->field('excerpt', 'Excerpt', 'textarea'),
                ],
            ],
            'treatments' => [
                'title' => 'Treatments',
                'search' => ['name', 'slug', 'summary'],
                'columns' => $this->columns(['name' => 'Treatment', 'slug' => 'Slug', 'summary' => 'Summary', 'image' => 'Image']),
                'fields' => [
                    $this->field('name', 'Treatment name', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('summary', 'Summary'),
                    $this->field('image', 'Image URL', 'url'),
                ],
            ],
            'technologies' => [
                'title' => 'Technologies',
                'search' => ['name', 'summary', 'icon'],
                'columns' => $this->columns(['name' => 'Name', 'summary' => 'Summary', 'icon' => 'Icon']),
                'fields' => [
                    $this->field('name', 'Name', 'text', required: true),
                    $this->field('summary', 'Summary'),
                    $this->field('icon', 'Icon'),
                ],
            ],
            'awards' => [
                'title' => 'Awards & Accreditations',
                'search' => ['title', 'issuer', 'year'],
                'columns' => $this->columns(['title' => 'Title', 'issuer' => 'Issuer', 'year' => 'Year', 'icon' => 'Icon']),
                'fields' => [
                    $this->field('title', 'Title', 'text', required: true),
                    $this->field('issuer', 'Issuer'),
                    $this->field('year', 'Year', 'number'),
                    $this->field('icon', 'Icon'),
                ],
            ],
            'job-openings' => [
                'title' => 'Job Openings',
                'description' => 'Career opportunities shown on the public careers page.',
                'search' => ['title', 'slug', 'location', 'department', 'type'],
                'columns' => $this->columns(['title' => 'Title', 'department' => 'Dept', 'location' => 'Location', 'type' => 'Type']),
                'fields' => [
                    $this->field('title', 'Job title', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('location', 'Location', 'text', required: true),
                    $this->field('type', 'Type', 'select', required: true, options: $this->optionList(['full-time', 'part-time', 'contract', 'remote'])),
                    $this->field('category', 'Category', 'select', options: $this->optionList(['clinical', 'allied-health', 'administration', 'technical', 'support'])),
                    $this->field('department', 'Department'),
                    $this->field('salary_range', 'Salary range'),
                    $this->field('description', 'Description', 'textarea', required: true),
                    $this->field('requirements', 'Requirements', 'textarea'),
                    $this->field('benefits', 'Benefits', 'textarea'),
                    $this->field('application_url', 'External application URL', 'url'),
                    $this->field('closing_date', 'Closing date', 'date'),
                    $this->field('is_active', 'Active?', 'boolean'),
                    $this->field('order', 'Sort order', 'number'),
                ],
            ],
            'job-applications' => [
                'title' => 'Job Applications',
                'description' => 'Applications submitted via the public careers page.',
                'search' => ['name', 'email', 'phone', 'status'],
                'columns' => $this->columns(['name' => 'Applicant', 'email' => 'Email', 'job_title' => 'Position', 'status' => 'Status', 'created_at' => 'Applied']),
                'fields' => [
                    $this->field('name', 'Full name', 'text', required: true),
                    $this->field('email', 'Email', 'email', required: true),
                    $this->field('phone', 'Phone'),
                    $this->field('cover_letter', 'Cover letter', 'textarea'),
                    $this->field('resume_url', 'Resume URL', 'url'),
                    $this->field('status', 'Status', 'select', options: $this->statusOptions(['new', 'reviewed', 'interviewed', 'offered', 'hired', 'rejected'])),
                ],
            ],
            'insurance' => [
                'title' => 'Insurance Partners',
                'search' => ['name', 'logo'],
                'columns' => $this->columns(['name' => 'Insurer', 'logo' => 'Logo URL']),
                'fields' => [
                    $this->field('name', 'Insurer name', 'text', required: true),
                    $this->field('logo', 'Logo URL', 'url'),
                ],
            ],
            'faqs' => [
                'title' => 'FAQs',
                'search' => ['question', 'answer', 'category'],
                'columns' => $this->columns(['question' => 'Question', 'category' => 'Category', 'order' => 'Order']),
                'fields' => [
                    $this->field('question', 'Question', 'text', required: true),
                    $this->field('answer', 'Answer', 'textarea', required: true),
                    $this->field('category', 'Category'),
                    $this->field('order', 'Order', 'number'),
                ],
            ],
            'menus' => [
                'title' => 'Navigation Menus',
                'description' => 'Manage top-level header menu entries and nested items.',
                'search' => ['title', 'slug', 'url', 'type'],
                'columns' => $this->columns(['title' => 'Title', 'slug' => 'Slug', 'type' => 'Type', 'url' => 'URL']),
                'fields' => [
                    $this->field('parent_id', 'Parent ID', 'number', helper: 'Leave blank for a top-level menu item.'),
                    $this->field('title', 'Title', 'text', required: true),
                    $this->field('slug', 'Slug', 'text', required: true),
                    $this->field('type', 'Type', 'select', required: true, options: $this->optionList(['link', 'dropdown', 'mega', 'external'])),
                    $this->field('url', 'URL'),
                    $this->field('icon', 'Icon'),
                    $this->field('description', 'Description', 'textarea'),
                    $this->field('order', 'Order', 'number'),
                ],
            ],
            'pages' => [
                'title' => 'CMS Pages',
                'description' => 'Pages assembled from reusable content blocks. Visible at /pages/<slug>.',
                'search' => ['title', 'slug', 'meta_title'],
                'columns' => $this->columns(['title' => 'Title', 'slug' => 'Slug', 'meta_title' => 'Meta title']),
                'fields' => [
                    $this->field('slug', 'Slug', 'text', required: true, helper: 'URL fragment, e.g. about-us'),
                    $this->field('title', 'Title', 'text', required: true),
                    $this->field('meta_title', 'Meta title (SEO)'),
                    $this->field('meta_description', 'Meta description (SEO)', 'textarea'),
                    $this->field('og_image', 'Open Graph image URL', 'url'),
                    $this->field('blocks', 'Page blocks', 'json', placeholder: '[{"type":"hero","data":{"title":"..."}}]', helper: 'Array of block objects: hero, richText, image, gallery, video, cta, stats, faq.'),
                ],
            ],
            'settings' => [
                'title' => 'Site Settings',
                'description' => 'Global site information used in headers, footers, metadata, and all page content.',
                'search' => ['site_name', 'email', 'primary_phone'],
                'columns' => $this->columns(['site_name' => 'Site name', 'email' => 'Email', 'primary_phone' => 'Phone']),
                'fields' => [
                    $this->field('site_name', 'Site name', 'text', required: true),
                    $this->field('tagline', 'Tagline'),
                    $this->field('logo_text', 'Logo letter', helper: 'Single letter shown in the header logo block.'),
                    $this->field('emergency_phone', 'Emergency phone'),
                    $this->field('primary_phone', 'Primary phone'),
                    $this->field('email', 'Contact email', 'email'),
                    $this->field('address', 'Street address', 'textarea'),
                    $this->field('facebook', 'Facebook URL', 'url'),
                    $this->field('twitter', 'Twitter / X URL', 'url'),
                    $this->field('instagram', 'Instagram URL', 'url'),
                    $this->field('linkedin', 'LinkedIn URL', 'url'),
                    $this->field('youtube', 'YouTube URL', 'url'),
                    $this->field('topbar', 'Top Bar', 'json', placeholder: '{"emergency_text":"24/7 Emergency Response","trauma_text":"Level-1 Trauma Center","phone":"1-800-123-4567","patient_portal_label":"Patient Portal","patient_portal_url":"/pages/patient-portal"}'),
                    $this->field('header', 'Header', 'json', placeholder: '{"logo_text":"Shubham International","find_doctor_label":"Find Doctor","book_appointment_label":"Book Appointment"}'),
                    $this->field('footer', 'Footer', 'json', placeholder: '{"tagline":"Defining the standard of clinical excellence...","copyright":"Shubham International Hospital. All rights reserved.","accreditations":["NABH Accredited","JCI Certified","ISO 9001:2015"]}'),
                    $this->field('hero', 'Hero ER Widget', 'json', placeholder: '{"wait_label":"Avg wait:","wait_value":"8 min","status_label":"ER status"}'),
                    $this->field('home_sections', 'Homepage Section Headers', 'json', placeholder: '{"services_eyebrow":"Services","services_title":"Comprehensive care, end to end","services_subtitle":"From emergency response...","departments_eyebrow":"Centers of Excellence","departments_title":"Specialized care across 40+ medical fields","doctors_eyebrow":"Our Specialists","doctors_title":"Meet the doctors setting new standards","doctors_subtitle":"Internationally trained physicians...","packages_eyebrow":"Preventative Care","packages_title":"Health packages built around you","packages_subtitle":"Same-day comprehensive screenings...","treatments_eyebrow":"Featured Treatments","treatments_title":"Advanced procedures, refined outcomes","technology_eyebrow":"Medical Technology","technology_title":"Investments that change outcomes","technology_subtitle":"From robotic surgery suites...","why_choose_eyebrow":"Why Shubham International","why_choose_title":"The difference is in the details","testimonials_eyebrow":"Patient Voices","testimonials_title":"Heard in their own words","stories_eyebrow":"Patient Stories","stories_title":"Real journeys, real outcomes","insurance_eyebrow":"Insurance & TPA","insurance_title":"Cashless treatment with leading insurers","awards_eyebrow":"Recognition","awards_title":"Awards & accreditation","blogs_eyebrow":"Health Library","blogs_title":"Latest from our doctors","faq_eyebrow":"Patient FAQs","faq_title":"Answers to the questions we hear most.","faq_subtitle":"Can\'t find what you\'re looking for? Our patient liaison team is available 24/7.","contact_cta_title":"Ready to plan your visit?","contact_cta_subtitle":"Speak with a patient liaison at...","contact_cta_book_label":"Book Appointment","contact_cta_contact_label":"Contact us"}'),
                    $this->field('about', 'About Section', 'json', placeholder: '{"eyebrow":"About","stat_value":"25+","stat_label":"Years of clinical excellence","points":["JCI & NABH accredited multi-specialty network","Multidisciplinary teams across 40+ medical fields","International patient services in 12 languages"],"learn_more_label":"Learn more about us"}'),
                    $this->field('career_stats', 'Homepage Career Stats', 'json', placeholder: '{"eyebrow":"Careers","openings_label":"Explore openings","stats":[{"value":"1,200+","label":"Clinicians"},{"value":"40+","label":"Specialties"},{"value":"12","label":"Countries"}]}'),
                    $this->field('contact_page', 'Contact Page', 'json', placeholder: '{"eyebrow":"Contact","title":"We\'re here for you, 24/7","patient_helpline_label":"Patient helpline","patient_helpline":"1-800-123-4567","emergency_label":"Emergency","emergency_phone":"1-800-999-9999","email_label":"Email","email":"care@lumina.health","opd_label":"OPD hours","opd_hours":"Mon\u2013Sat \u00b7 8 AM \u2013 8 PM","location_label":"Main hospital","address":"1500 Medical Plaza, New York, NY 10001","map_placeholder":"Interactive map placeholder"}'),
                    $this->field('appointment_sidebar', 'Appointment Sidebar', 'json', placeholder: '{"call_label":"Call us","helpline":"1-800-123-4567","helpline_note":"24/7 patient helpline","hours_label":"OPD Hours","hours":"Mon\u2013Sat \u00b7 8:00 AM \u2013 8:00 PM","emergency_note":"Emergency 24/7","location_label":"Location","location":"1500 Medical Plaza","location_city":"New York, NY"}'),
                    $this->field('careers_page', 'Careers Page Content', 'json', placeholder: '{"hero_eyebrow":"Careers","hero_title":"Your Career. Our Mission. Together, We Heal.","hero_subtitle":"At Shubham International Hospital...","why_eyebrow":"Why Shubham International","why_title":"More than a workplace. A mission.","why_subtitle":"We offer rich opportunities...","why_items":[{"icon":"users","title":"Collaborative Culture","text":"Work alongside internationally trained specialists..."},{"icon":"edit","title":"Growth & Development","text":"Continuous learning opportunities..."},{"icon":"heart","title":"Comprehensive Benefits","text":"Competitive compensation..."},{"icon":"activity","title":"Impactful Work","text":"Every role contributes to our mission..."}],"contact_eyebrow":"Get in Touch","contact_title":"Have questions about your next career move?","contact_subtitle":"Our HR team is here to help...","contact_phone_label":"Phone","contact_phone":"+977-9809090909","contact_email_label":"Email","contact_email":"careers@shubham.intl","contact_address_label":"Address","contact_address":"Manamaiju, Kathmandu, Nepal","search_placeholder":"Search jobs by title, department, or location\u2026","search_cta":"View All Openings"}'),
                    $this->field('theme', 'Theme Colors', 'json', placeholder: '{"primary":"oklch(0.38 0.11 250)","primary_foreground":"oklch(0.99 0.003 240)","primary_soft":"oklch(0.95 0.025 250)","secondary":"oklch(0.62 0.10 195)","secondary_foreground":"oklch(0.99 0.003 240)","secondary_soft":"oklch(0.95 0.02 195)","emergency":"oklch(0.55 0.20 17)","emergency_foreground":"oklch(0.99 0.003 240)","emergency_soft":"oklch(0.96 0.03 17)"}', helper: 'Override brand colors using oklch values. Leave empty to use CSS defaults.'),
                ],
            ],
            'admin-users' => [
                'title' => 'Admin Users',
                'description' => 'People who can sign into this admin panel.',
                'search' => ['name', 'email', 'role'],
                'columns' => $this->columns(['name' => 'Name', 'email' => 'Email', 'role' => 'Role']),
                'fields' => [
                    $this->field('name', 'Full name', 'text', required: true),
                    $this->field('email', 'Email', 'email', required: true),
                    $this->field('password', 'Password', 'text', helper: 'Leave blank when editing to keep the current password.'),
                    $this->field('role', 'Role', 'select', required: true, options: $this->optionList(['super-admin', 'editor', 'viewer'])),
                ],
            ],
        ];

        return $configs[$this->resource] ?? abort(404);
    }

    protected function field(
        string $name,
        string $label,
        string $type = 'text',
        bool $required = false,
        array $options = [],
        ?string $placeholder = null,
        ?string $helper = null,
        mixed $default = null,
    ): array {
        return compact('name', 'label', 'type', 'required', 'options', 'placeholder', 'helper', 'default');
    }

    protected function columns(array $columns): array
    {
        return collect($columns)
            ->map(fn ($label, $key) => ['key' => $key, 'label' => $label])
            ->values()
            ->all();
    }

    protected function optionList(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => ['value' => $value, 'label' => ucwords(str_replace('-', ' ', $value))])
            ->all();
    }

    protected function statusOptions(array $values): array
    {
        return $this->optionList($values);
    }

    // Uploaded images/CVs are stored as base64 data URIs (Vercel has no
    // persistent /storage); external http(s) URLs are passed through.
    protected function displayUrl(mixed $value): string
    {
        if (is_string($value) && (str_starts_with($value, 'http') || str_starts_with($value, 'data:'))) {
            return $value;
        }

        return Storage::url((string) $value);
    }
};

?>
<div class="space-y-5">
    <x-feedback.flash key="message" variant="success" />

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ $title }}</h2>
            @if($description)
                <p class="text-sm text-muted-foreground mt-1">{{ $description }}</p>
            @endif
        </div>
        <x-ui.button wire:click="create">
            @svg('lucide-plus', 'h-4 w-4')
            New
        </x-ui.button>
    </div>

    <div class="bg-surface rounded-xl border border-border overflow-hidden">
        <div class="p-3 border-b border-border flex items-center gap-2">
            <x-form.search-input
                type="text"
                variant="admin"
                wire:model.live.debounce="search"
                placeholder="Search…"
                class="flex-1 max-w-sm"
            />
            <div class="text-xs text-muted-foreground">{{ $items->total() }} of {{ $total }}</div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                    <tr>
                        @foreach($columns as $col)
                            <th class="text-left px-4 py-3 font-semibold">{{ $col['label'] }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right font-semibold w-1">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($items as $item)
                        <tr class="hover:bg-muted/30">
                            @foreach($columns as $col)
                                <td class="px-4 py-3 align-top max-w-xs">
                                    @php $val = data_get($item, $col['key']); @endphp
                                    @if(($col['type'] ?? 'text') === 'image' && $val)
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full overflow-hidden bg-muted shrink-0">
                                                <img src="{{ $this->displayUrl($val) }}" alt="" class="size-full object-cover" loading="lazy" />
                                            </div>
                                        </div>
                                    @elseif(($col['type'] ?? 'text') === 'image_text')
                                        <div class="flex items-center gap-3">
                                            @if($val)
                                                <div class="size-10 rounded-full overflow-hidden bg-muted shrink-0">
                                                    <img src="{{ $this->displayUrl($val) }}" alt="{{ data_get($item, 'name') ?? data_get($item, 'title') ?? '' }}" class="size-full object-cover" loading="lazy" />
                                                </div>
                                            @else
                                                <div class="size-10 rounded-full bg-muted shrink-0 grid place-items-center text-xs text-muted-foreground font-semibold">
                                                    {{ substr(data_get($item, 'name') ?? data_get($item, 'title') ?? '?', 0, 2) }}
                                                </div>
                                            @endif
                                            <div class="truncate font-medium">
                                                {{ data_get($item, 'name') ?? data_get($item, 'title') ?? '' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="truncate">
                                            {{ is_array($val) ? implode(', ', $val) : (is_bool($val) ? ($val ? 'Yes' : 'No') : $val ?? '—') }}
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($viewable)
                                    <button wire:click="showDetails(@js($item->getKey()))" class="p-1.5 rounded hover:bg-muted text-foreground/70 hover:text-foreground" aria-label="View details">
                                        @svg('lucide-eye', 'h-4 w-4')
                                    </button>
                                @endif
                                <button wire:click="edit(@js($item->getKey()))" class="p-1.5 rounded hover:bg-muted text-foreground/70 hover:text-foreground" aria-label="Edit">
                                    @svg('lucide-pencil', 'h-4 w-4')
                                </button>
                                <button wire:click="delete(@js($item->getKey()))" wire:confirm="Delete this record?" class="p-1.5 rounded hover:bg-muted text-foreground/70 hover:text-emergency ml-1" aria-label="Delete">
                                    @svg('lucide-trash-2', 'h-4 w-4')
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="px-4 py-12 text-center text-muted-foreground text-sm">No records yet. Click New to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    @if($showViewModal && $viewItem)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
            <button type="button" aria-label="Close" wire:click="closeView" class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"></button>
            <div class="relative bg-surface rounded-t-2xl sm:rounded-2xl w-full sm:max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-elevated animate-fade-up">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold">{{ rtrim($title, 's') }} Details</h3>
                    <button type="button" wire:click="closeView" class="text-muted-foreground hover:text-foreground text-sm">Close</button>
                </div>
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($fields as $field)
                            @php
                                $val = data_get($viewItem, $field['name']);
                                $isResume = str_contains($field['name'], 'resume');
                            @endphp
                            @if($field['type'] === 'textarea' && $val)
                                <div class="sm:col-span-2">
                                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $field['label'] }}</span>
                                    <p class="mt-1 text-sm whitespace-pre-wrap bg-muted/40 rounded-lg p-4">{{ $val }}</p>
                                </div>
                            @elseif($field['type'] === 'json' && $val)
                                <div class="sm:col-span-2">
                                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $field['label'] }}</span>
                                    <pre class="mt-1 text-xs font-mono bg-muted/40 rounded-lg p-4 overflow-x-auto">{{ is_string($val) ? $val : json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </div>
                            @elseif($isResume)
                                <div class="sm:col-span-2">
                                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $field['label'] }}</span>
                                    @if($val)
                                        <div class="mt-2 flex items-center gap-3 p-4 rounded-lg border border-border bg-muted/20">
                                            @svg('lucide-file-text', 'h-8 w-8 text-primary shrink-0')
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium truncate">{{ str_starts_with($val, 'data:') ? 'Uploaded document' : basename($val) }}</p>
                                                <p class="text-xs text-muted-foreground">Uploaded document</p>
                                            </div>
                                            <a href="{{ $this->displayUrl($val) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-1.5 rounded-md bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground hover:opacity-90 shrink-0">
                                                @svg('lucide-download', 'h-3.5 w-3.5')
                                                Download
                                            </a>
                                        </div>
                                    @else
                                        <p class="mt-1 text-sm text-muted-foreground">No file uploaded.</p>
                                    @endif
                                </div>
                            @elseif($field['type'] === 'image' && $val)
                                <div>
                                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $field['label'] }}</span>
                                    <div class="mt-1">
                                        <img src="{{ $this->displayUrl($val) }}" class="h-20 w-20 rounded-lg object-cover border border-border" />
                                    </div>
                                </div>
                            @elseif($field['type'] === 'url' && $val)
                                <div>
                                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $field['label'] }}</span>
                                    <p class="mt-0.5 text-sm truncate"><a href="{{ $val }}" target="_blank" class="text-primary hover:underline">{{ $val }}</a></p>
                                </div>
                            @else
                                <div>
                                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $field['label'] }}</span>
                                    <p class="mt-0.5 text-sm">{{ is_array($val) ? implode(', ', $val) : (is_bool($val) ? ($val ? 'Yes' : 'No') : ($val ?? '—')) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if($viewItem->created_at)
                        <div class="pt-3 border-t border-border flex gap-6 text-xs text-muted-foreground">
                            <span>Created: {{ $viewItem->created_at->format('M j, Y g:i A') }}</span>
                            @if($viewItem->updated_at && $viewItem->updated_at != $viewItem->created_at)
                                <span>Updated: {{ $viewItem->updated_at->format('M j, Y g:i A') }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($modalOpen)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
            <button type="button" aria-label="Close" wire:click="closeModal" class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"></button>
            <div class="relative bg-surface rounded-t-2xl sm:rounded-2xl w-full sm:max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-elevated animate-fade-up">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold">{{ $modalTitle }}</h3>
                    <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground text-sm">Close</button>
                </div>
                <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    @foreach($fields as $field)
                        @php $name = $field['name']; $helper = $field['helper'] ?: null; @endphp

                        @if($field['type'] === 'textarea')
                            <x-form.textarea variant="admin" :label="$field['label']" :required="$field['required']" :hint="$helper" wire:model="form.{{ $name }}" rows="4" placeholder="{{ $field['placeholder'] ?? '' }}" />
                        @elseif($field['type'] === 'select')
                            <x-form.select variant="admin" :label="$field['label']" :required="$field['required']" :hint="$helper" wire:model="form.{{ $name }}">
                                <option value="">— Select —</option>
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </x-form.select>
                        @elseif($field['type'] === 'boolean')
                            <x-form.select variant="admin" :label="$field['label']" :hint="$helper" wire:model="form.{{ $name }}">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </x-form.select>
                        @elseif($field['type'] === 'json')
                            <div>
                                <x-form.label class="mb-1.5" :required="$field['required']">{{ $field['label'] }}</x-form.label>
                                <textarea
                                    wire:model="form.{{ $name }}"
                                    rows="6"
                                    placeholder="{{ $field['placeholder'] ?? '{ }' }}"
                                    class="w-full px-3 py-2 text-xs font-mono rounded-md bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30"
                                ></textarea>
                                @if($helper)
                                    <p class="text-xs text-muted-foreground mt-1">{{ $helper }}</p>
                                @endif
                            </div>
                        @elseif($field['type'] === 'image')
                            <div>
                                <x-form.label class="mb-1.5" :required="$field['required']">{{ $field['label'] }}</x-form.label>
                                @php
                                    $existingImage = is_string($form[$field['name']] ?? null) ? $form[$field['name']] : null;
                                    $tempUpload = ($form[$field['name']] ?? null) instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $form[$field['name']] : null;
                                @endphp
                                @if($existingImage || $tempUpload)
                                    <div class="mb-2">
                                        @if($tempUpload)
                                            <img src="{{ $tempUpload->temporaryUrl() }}" class="h-24 w-24 rounded-lg object-cover border border-border" />
                                        @else
                                            <img src="{{ $this->displayUrl($existingImage) }}" class="h-24 w-24 rounded-lg object-cover border border-border" />
                                        @endif
                                    </div>
                                @endif
                                <input
                                    type="file"
                                    wire:model="form.{{ $field['name'] }}"
                                    accept="image/jpeg,image/png,image/webp,image/gif"
                                    class="w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-primary-foreground hover:file:opacity-90"
                                />
                                @if($helper)
                                    <p class="text-xs text-muted-foreground mt-1">{{ $helper }}</p>
                                @endif
                            </div>
                        @elseif(str_contains($name, 'resume'))
                            {{-- Resume/CV is stored as a base64 data URI — render read-only, never dump the blob into a text input --}}
                            @php $resumeVal = $form[$name] ?? ''; $isDataResume = is_string($resumeVal) && str_starts_with($resumeVal, 'data:'); @endphp
                            <div>
                                <x-form.label class="mb-1.5">{{ $field['label'] }}</x-form.label>
                                @if($resumeVal)
                                    <div class="flex items-center gap-3 p-4 rounded-lg border border-border bg-muted/20">
                                        @svg('lucide-file-text', 'h-8 w-8 text-primary shrink-0')
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium truncate">{{ $isDataResume ? 'Uploaded document' : basename($resumeVal) }}</p>
                                            <p class="text-xs text-muted-foreground">{{ $isDataResume ? 'Saved as embedded file' : 'Uploaded document' }}</p>
                                        </div>
                                        <a href="{{ $this->displayUrl($resumeVal) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-1.5 rounded-md bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground hover:opacity-90 shrink-0">
                                            @svg('lucide-download', 'h-3.5 w-3.5')
                                            Download
                                        </a>
                                    </div>
                                @else
                                    <p class="mt-1 text-sm text-muted-foreground">No file uploaded.</p>
                                @endif
                                @if($helper)
                                    <p class="text-xs text-muted-foreground mt-1">{{ $helper }}</p>
                                @endif
                            </div>
                        @else
                            <x-form.input variant="admin" type="{{ $field['type'] === 'tags' ? 'text' : $field['type'] }}" :label="$field['label']" :required="$field['required']" :hint="$helper" wire:model="form.{{ $name }}" placeholder="{{ $field['placeholder'] ?? '' }}" />
                        @endif
                    @endforeach

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md text-sm font-medium hover:bg-muted">Cancel</button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-4 py-2 rounded-md text-sm font-semibold bg-primary text-primary-foreground shadow-card hover:opacity-90 disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">Save</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
