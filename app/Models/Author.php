<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'slug', 'name', 'photo', 'bio', 'specialty',
    ];

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }
}
