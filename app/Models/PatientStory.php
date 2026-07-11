<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientStory extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'patient', 'image', 'url',
    ];
}
