<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuItem extends Model
{
    public const CACHE_KEY_TOP = 'site_menus_top';
    public const CACHE_KEY_FOOTER = 'site_menus_footer';

    /**
     * Top-level nav items with children — cached forever, invalidated on menus save.
     */
    public static function cachedTopLevel()
    {
        return Cache::rememberForever(self::CACHE_KEY_TOP, fn () => static::with('children')->whereNull('parent_id')->orderBy('order')->get());
    }

    /**
     * Footer menu trees (keyed by slug) — one query instead of three.
     */
    public static function cachedFooter(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever(self::CACHE_KEY_FOOTER, fn () => static::with('children')->whereIn('slug', ['patients', 'about', 'wellness'])->get()->keyBy('slug'));
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY_TOP);
        Cache::forget(self::CACHE_KEY_FOOTER);
    }

    protected $fillable = [
        'parent_id', 'title', 'slug', 'type', 'url', 'icon', 'description', 'order',
    ];

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }
}
