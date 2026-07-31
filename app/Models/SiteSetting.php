<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'logo_text',
        'emergency_phone',
        'primary_phone',
        'email',
        'address',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'topbar',
        'header',
        'footer',
        'hero',
        'home_sections',
        'about',
        'career_stats',
        'contact_page',
        'appointment_sidebar',
        'careers_page',
        'theme',
    ];

    protected function casts(): array
    {
        return [
            'topbar' => 'array',
            'header' => 'array',
            'footer' => 'array',
            'hero' => 'array',
            'home_sections' => 'array',
            'about' => 'array',
            'career_stats' => 'array',
            'contact_page' => 'array',
            'appointment_sidebar' => 'array',
            'careers_page' => 'array',
            'theme' => 'array',
        ];
    }
}
