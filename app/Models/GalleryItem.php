<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'type', 'title', 'url', 'thumbnail', 'category',
    ];
}
