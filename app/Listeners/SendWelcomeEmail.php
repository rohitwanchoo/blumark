<?php

namespace App\Listeners;

use App\Mail\NewUserNotificationMail;
use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(Registered $event): void
    {
        // Send welcome email to the new user
        Mail::to($event->user->email)->send(new WelcomeMail($event->user));

        // Send notification to super admin
        $superAdminEmail = config('app.super_admin_email');
        if ($superAdminEmail) {
            Mail::to($superAdminEmail)->send(new NewUserNotificationMail($event->user));
        }
    }
}
