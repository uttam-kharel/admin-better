<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    /** Cache key for the single settings row (read on every page, in several components). */
    public const CACHE_KEY = 'site_settings';

    /**
     * The settings row, cached forever and invalidated whenever settings are saved
     * (see the resource manager's save hook). Saves ~5 queries per page load.
     */
    public static function cached(): ?self
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::first());
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

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
