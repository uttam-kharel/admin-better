<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'designation',
        'department_slug',
        'department',
        'qualifications',
        'experience_years',
        'languages',
        'photo',
        'bio',
        'expertise',
        'schedule',
        'publications',
    ];

    protected $casts = [
        'qualifications' => 'array',
        'languages' => 'array',
        'expertise' => 'array',
        'schedule' => 'array',
        'publications' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_slug', 'slug');
    }
}
