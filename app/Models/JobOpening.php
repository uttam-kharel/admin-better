<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('closing_date')
                  ->orWhere('closing_date', '>=', now());
            });
    }
}
