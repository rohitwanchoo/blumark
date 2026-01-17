<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'price_cents',
        'billing_period',
        'jobs_limit',
        'pages_per_job_limit',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'jobs_limit' => 'integer',
            'pages_per_job_limit' => 'integer',
        ];
    }

    public function getPriceFormatted(): string
    {
        if ($this->price_cents === 0) {
            return 'Free';
        }
        return '$' . number_format($this->price_cents / 100, 2);
    }

    public function getMonthlyPrice(): string
    {
        if ($this->price_cents === 0) {
            return 'Free';
        }
        return '$' . number_format($this->price_cents / 100, 0);
    }

    public function isUnlimited(): bool
    {
        return $this->jobs_limit === null && $this->pages_per_job_limit === null;
    }

    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
