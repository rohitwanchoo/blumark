<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lender extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'first_name',
        'last_name',
        'email',
        'email_2',
        'email_3',
        'phone',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function distributionItems(): HasMany
    {
        return $this->hasMany(LenderDistributionItem::class);
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);
        return implode(' ', $parts) ?: $this->company_name;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->first_name || $this->last_name) {
            return $this->full_name . ' (' . $this->company_name . ')';
        }
        return $this->company_name;
    }

    public function getAllEmailsAttribute(): array
    {
        return array_filter([
            $this->email,
            $this->email_2,
            $this->email_3,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function toSnapshotArray(): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_2' => $this->email_2,
            'email_3' => $this->email_3,
            'full_name' => $this->full_name,
        ];
    }
}
