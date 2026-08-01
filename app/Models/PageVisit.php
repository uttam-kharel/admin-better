<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = [
        'path', 'query', 'full_url', 'referer', 'visitor_id', 'ip_hash',
        'user_agent', 'device', 'browser', 'is_unique',
    ];

    protected $casts = [
        'is_unique' => 'boolean',
    ];
}
