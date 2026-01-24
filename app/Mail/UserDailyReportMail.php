<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserDailyReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public array $userData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Daily BluMark Activity Summary - ' . now()->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user.daily-report',
            with: [
                'user' => $this->user,
                'userData' => $this->userData,
                'reportDate' => now()->format('F d, Y'),
                'dashboardUrl' => route('dashboard'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
