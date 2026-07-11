<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_opening_id',
        'name',
        'email',
        'phone',
        'cover_letter',
        'resume_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function jobOpening()
    {
        return $this->belongsTo(JobOpening::class);
    }

    public function getJobTitleAttribute()
    {
        return $this->jobOpening?->title ?? '—';
    }
}
