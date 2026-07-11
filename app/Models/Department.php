<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'tagline',
        'description',
        'icon',
        'image',
        'treatments',
        'facilities',
    ];

    protected $casts = [
        'treatments' => 'array',
        'facilities' => 'array',
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'department_slug', 'slug');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'department_slug', 'slug');
    }
}
