<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'eyebrow',
        'title',
        'subtitle',
        'image',
        'cta_label',
        'cta_url',
        'secondary_cta_label',
        'secondary_cta_url',
        'order',
    ];
}
