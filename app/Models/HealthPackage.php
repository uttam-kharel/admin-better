<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthPackage extends Model
{
    protected $fillable = [
        'slug', 'name', 'tier', 'price', 'original_price', 'currency',
        'description', 'inclusions', 'is_popular',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'is_popular' => 'boolean',
    ];
}
