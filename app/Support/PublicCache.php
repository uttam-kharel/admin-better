<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Cache keys for public-site content that is expensive to build and changes
 * only through the admin. Every key here is invalidated on ANY admin save or
 * delete (see the resource manager), plus a 1h TTL backstop for direct DB edits.
 */
class PublicCache
{
    public const HOMEPAGE = 'site_homepage';

    public const SERVICES = 'site_services';

    public const DEPARTMENTS = 'site_departments';

    public const TTL = 3600;

    public static function keys(): array
    {
        return [self::HOMEPAGE, self::SERVICES, self::DEPARTMENTS];
    }

    public static function flush(): void
    {
        foreach (self::keys() as $key) {
            Cache::forget($key);
        }
    }
}
