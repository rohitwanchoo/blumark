<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyUserReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public array $reportData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily User Activity Report - ' . now()->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.daily-user-report',
            with: [
                'reportData' => $this->reportData,
                'reportDate' => now()->format('F d, Y'),
                'dashboardUrl' => config('app.url') . '/admin/dashboard',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
