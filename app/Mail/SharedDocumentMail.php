<?php

namespace App\Mail;

use App\Models\SharedLink;
use App\Models\User;
use App\Models\WatermarkJob;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SharedDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public SharedLink $sharedLink;
    public WatermarkJob $job;
    public User $sender;

    public function __construct(SharedLink $sharedLink, WatermarkJob $job, User $sender)
    {
        $this->sharedLink = $sharedLink;
        $this->job = $job;
        $this->sender = $sender;
    }

    public function envelope(): Envelope
    {
        $senderName = $this->sender->getFullName();
        $settings = $this->job->settings ?? [];
        $iso = $settings['iso'] ?? 'Unknown ISO';

        return new Envelope(
            subject: "Document shared by {$iso}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shared-document',
            with: [
                'downloadUrl' => $this->sharedLink->getUrl(),
                'expiresAt' => $this->sharedLink->expires_at->format('F j, Y'),
                'senderName' => $this->sender->getFullName(),
                'senderCompany' => $this->sender->company_name ?? ($this->job->settings['iso'] ?? ''),
                'filename' => $this->job->original_filename,
                'hasPassword' => $this->sharedLink->hasPassword(),
                'recipientName' => $this->sharedLink->recipient_name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
