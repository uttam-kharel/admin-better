<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JobOpening extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'location',
        'type',
        'category',
        'department',
        'salary_range',
        'description',
        'requirements',
        'benefits',
        'application_url',
        'closing_date',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'closing_date' => 'date',
        ];
    }

    public function scopeActive($query)
    {
        // DB literal, not a PHP bool: PDO binds true as integer 1, which Postgres
        // rejects for boolean columns ("operator does not exist: boolean = integer").
        return $query->where('is_active', DB::raw('TRUE'));
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', DB::raw('TRUE'))
            ->where(function ($q) {
                $q->whereNull('closing_date')
                  ->orWhere('closing_date', '>=', now());
            });
    }
}
