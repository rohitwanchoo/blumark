<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New User Registration - ' . $this->user->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-user-notification',
            with: [
                'user' => $this->user,
                'registeredAt' => $this->user->created_at->format('M d, Y g:i A'),
                'userUrl' => config('app.url') . '/admin/users/' . $this->user->id,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
