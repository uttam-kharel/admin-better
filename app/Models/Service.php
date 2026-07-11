<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'summary',
        'description',
        'icon',
        'department_slug',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_slug', 'slug');
    }
}
