<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'content', 'category', 'author',
        'author_id', 'published_at', 'read_minutes', 'image', 'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    public function authorInfo()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }
}
