<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class HttpsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (App::environment('production') && config('app.force_https', false)) {
            URL::forceScheme('https');
            
            // Force secure cookies
            config([
                'session.secure' => true,
                'session.same_site' => 'strict',
            ]);
            
            // Force secure mail configuration
            config([
                'mail.mailers.smtp.scheme' => 'tls',
            ]);
        }
    }
}
