<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPack extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'credits',
        'price_cents',
        'bonus_credits',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getTotalCredits(): int
    {
        return $this->credits + $this->bonus_credits;
    }

    public function getPriceFormatted(): string
    {
        return '$' . number_format($this->price_cents / 100, 0);
    }

    public function getPricePerCredit(): float
    {
        $total = $this->getTotalCredits();
        return $total > 0 ? round($this->price_cents / 100 / $total, 2) : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
