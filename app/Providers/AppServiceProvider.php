<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force Laravel to always use APP_URL for URL generation
        \URL::forceRootUrl(config('app.url'));
        // Also force HTTPS since ngrok uses https
        \URL::forceScheme('https');
    }
}
