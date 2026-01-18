<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Alert $alert
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $severityEmoji = match($this->alert->severity) {
            Alert::SEVERITY_CRITICAL => '🚨',
            Alert::SEVERITY_HIGH => '⚠️',
            Alert::SEVERITY_MEDIUM => '⚡',
            default => 'ℹ️',
        };

        $mail = (new MailMessage)
            ->subject("{$severityEmoji} [{$this->alert->severity}] Security Alert: {$this->alert->title}")
            ->greeting('Security Alert Detected')
            ->line("**Alert Type:** " . $this->alert->getTypeLabel())
            ->line("**Severity:** " . strtoupper($this->alert->severity))
            ->line("**Description:** " . $this->alert->description);

        if ($this->alert->ip_address) {
            $mail->line("**IP Address:** " . $this->alert->ip_address);
        }

        if ($this->alert->watermarkJob) {
            $mail->line("**Document:** " . $this->alert->watermarkJob->original_filename);
        }

        $mail->action('View Alert Details', url('/admin/alerts/' . $this->alert->id))
            ->line('Please investigate this activity promptly.');

        // Add urgency styling for critical/high alerts
        if (in_array($this->alert->severity, [Alert::SEVERITY_CRITICAL, Alert::SEVERITY_HIGH])) {
            $mail->error();
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'type' => $this->alert->type,
            'severity' => $this->alert->severity,
            'title' => $this->alert->title,
            'description' => $this->alert->description,
            'ip_address' => $this->alert->ip_address,
            'watermark_job_id' => $this->alert->watermark_job_id,
        ];
    }
}
