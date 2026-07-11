<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'slug', 'title', 'meta_title', 'meta_description', 'og_image', 'blocks',
    ];

    protected function blocks(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): array {
                if (is_array($value)) return $value;
                if (is_null($value)) return [];
                $decoded = json_decode($value, true);
                while (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                return $decoded ?? [];
            },
            set: fn(mixed $value): string => is_string($value) ? $value : json_encode($value),
        );
    }
}
