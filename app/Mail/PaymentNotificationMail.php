<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $paymentType,
        public array $paymentData
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Payment Received - ' . $this->user->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.payment-notification',
            with: [
                'user' => $this->user,
                'paymentType' => $this->paymentType,
                'paymentData' => $this->paymentData,
                'userUrl' => config('app.url') . '/admin/users/' . $this->user->id,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
