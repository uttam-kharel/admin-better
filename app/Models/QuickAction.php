<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickAction extends Model
{
    protected $fillable = [
        'icon', 'label', 'helper', 'url', 'tone',
    ];
}
