<?php

namespace App\Console\Commands;

use App\Models\SmtpSetting;
use App\Models\User;
use App\Services\CustomSmtpMailer;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestSmtpEmail extends Command
{
    protected $signature = 'smtp:test
                          {user_id : The user ID}
                          {to_email : The recipient email address}
                          {--smtp= : Optional SMTP setting ID to use}';

    protected $description = 'Send a test email using custom SMTP settings';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $toEmail = $this->argument('to_email');
        $smtpId = $this->option('smtp');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        // Get SMTP setting
        $smtpSetting = null;
        if ($smtpId) {
            $smtpSetting = SmtpSetting::where('id', $smtpId)
                ->where('user_id', $userId)
                ->first();
            if (!$smtpSetting) {
                $this->error("SMTP setting with ID {$smtpId} not found for this user.");
                return 1;
            }
        } else {
            $smtpSetting = SmtpSetting::getActiveForUser($userId);
            if (!$smtpSetting) {
                $this->error("No active SMTP setting found for user {$userId}.");
                return 1;
            }
        }

        $this->info("Using SMTP: {$smtpSetting->name}");
        $this->info("Host: {$smtpSetting->host}:{$smtpSetting->port}");
        $this->info("From: {$smtpSetting->from_email}");
        $this->info("To: {$toEmail}");
        $this->info("");
        $this->info("Sending test email...");

        try {
            CustomSmtpMailer::sendWithCustomSmtp(
                $userId,
                new TestMailable($user, $smtpSetting),
                $toEmail,
                $smtpSetting->id
            );

            $this->info("✓ Test email sent successfully!");
            $this->info("Check {$toEmail} for the test email.");

            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send test email:");
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());

            return 1;
        }
    }
}

class TestMailable extends Mailable
{
    public function __construct(
        public User $user,
        public SmtpSetting $smtpSetting
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                $this->smtpSetting->from_email,
                $this->smtpSetting->from_name
            ),
            subject: 'Test Email - Custom SMTP Configuration',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    private function buildHtml(): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $encryption = $this->smtpSetting->encryption ?: 'None';
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info { background-color: #e7f4ff; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ SMTP Test Email</h1>
        </div>
        <div class="content">
            <p>This is a test email sent from your custom SMTP configuration.</p>

            <div class="info">
                <strong>Status:</strong> If you're reading this, your custom SMTP is working correctly!
            </div>

            <h3>Configuration Details:</h3>
            <table>
                <tr>
                    <td>SMTP Profile:</td>
                    <td>{$this->smtpSetting->name}</td>
                </tr>
                <tr>
                    <td>Host:</td>
                    <td>{$this->smtpSetting->host}:{$this->smtpSetting->port}</td>
                </tr>
                <tr>
                    <td>Encryption:</td>
                    <td>{$encryption}</td>
                </tr>
                <tr>
                    <td>From Email:</td>
                    <td>{$this->smtpSetting->from_email}</td>
                </tr>
                <tr>
                    <td>From Name:</td>
                    <td>{$this->smtpSetting->from_name}</td>
                </tr>
                <tr>
                    <td>User:</td>
                    <td>{$this->user->name} (ID: {$this->user->id})</td>
                </tr>
                <tr>
                    <td>Timestamp:</td>
                    <td>{$timestamp}</td>
                </tr>
            </table>

            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Your custom SMTP configuration is working properly</li>
                <li>Emails sent through distributions will use this SMTP</li>
                <li>Check your email headers to verify the sender information</li>
            </ul>
        </div>
        <div class="footer">
            <p>This is an automated test email from your watermarking application.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
