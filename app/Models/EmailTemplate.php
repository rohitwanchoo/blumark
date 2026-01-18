<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'subject',
        'body',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Available placeholders for templates
     */
    public static array $placeholders = [
        '{lender_name}' => 'Lender company name',
        '{lender_contact}' => 'Lender contact name (first name or full name)',
        '{sender_name}' => 'Your name',
        '{sender_company}' => 'Your company name',
        '{document_name}' => 'Document/Distribution name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Render the subject with placeholders replaced
     */
    public function renderSubject(array $data): string
    {
        return $this->replacePlaceholders($this->subject, $data);
    }

    /**
     * Render the body with placeholders replaced
     */
    public function renderBody(array $data): string
    {
        return $this->replacePlaceholders($this->body, $data);
    }

    /**
     * Replace placeholders in text
     */
    protected function replacePlaceholders(string $text, array $data): string
    {
        $replacements = [
            '{lender_name}' => $data['lender_name'] ?? '',
            '{lender_contact}' => $data['lender_contact'] ?? '',
            '{sender_name}' => $data['sender_name'] ?? '',
            '{sender_company}' => $data['sender_company'] ?? '',
            '{document_name}' => $data['document_name'] ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Mark this template as default, removing default from others
     */
    public function makeDefault(): void
    {
        // Remove default from other templates for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Scope: for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: default templates only
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get or create a default template for a user
     */
    public static function getDefaultForUser(int $userId): ?static
    {
        return static::forUser($userId)->default()->first();
    }

    /**
     * Create default template for a user if none exists
     */
    public static function createDefaultForUser(int $userId): static
    {
        return static::create([
            'user_id' => $userId,
            'name' => 'Default Template',
            'subject' => 'Document from {sender_company}',
            'body' => "Dear {lender_contact},\n\nPlease find the attached document \"{document_name}\" from {sender_company}.\n\nIf you have any questions, please don't hesitate to reach out.\n\nBest regards,\n{sender_name}\n{sender_company}",
            'is_default' => true,
        ]);
    }
}
