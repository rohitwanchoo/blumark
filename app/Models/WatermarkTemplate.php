<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatermarkTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'iso',
        'lender',
        'lender_email',
        'font_size',
        'color',
        'opacity',
        'is_default',
        'usage_count',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'font_size' => 'integer',
        'opacity' => 'integer',
        'usage_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment usage count when template is used.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get watermark settings array for job creation.
     */
    public function getWatermarkSettings(): array
    {
        return [
            'type' => 'iso_lender',
            'iso' => $this->iso,
            'lender' => $this->lender,
            'font_size' => $this->font_size,
            'color' => $this->color,
            'opacity' => $this->opacity,
            'flatten_pdf' => true,
        ];
    }

    /**
     * Scope to get templates sorted by usage.
     */
    public function scopeMostUsed($query)
    {
        return $query->orderByDesc('usage_count');
    }

    /**
     * Scope to get user's default template.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
