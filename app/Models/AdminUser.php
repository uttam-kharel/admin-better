<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $fillable = [
        'email', 'name', 'role', 'password',
    ];

    protected $hidden = [
        'password',
    ];
}
