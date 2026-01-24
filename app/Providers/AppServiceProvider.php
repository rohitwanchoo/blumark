<?php

namespace App\Providers;

use App\Listeners\SendWelcomeEmail;
use App\Models\DocumentAccessLog;
use App\Observers\DocumentAccessLogObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production' || str_starts_with(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        // Configure Scramble OpenAPI documentation
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });

        // Register observers for suspicious activity detection
        DocumentAccessLog::observe(DocumentAccessLogObserver::class);

        // Register event listeners
        Event::listen(Registered::class, SendWelcomeEmail::class);

        // Register SocialiteProviders using closure for Laravel 11+
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('linkedin', \SocialiteProviders\LinkedIn\Provider::class);
        });
    }
}
