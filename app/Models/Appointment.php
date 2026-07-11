<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'phone', 'email', 'department_slug', 'doctor_slug',
        'preferred_date', 'message', 'status',
    ];
}
