<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Force Laravel to use correct base URL (without /public)
        if (request()->getSchemeAndHttpHost() === 'https://lydo.mswdopol.site') {
            URL::forceRootUrl('https://lydo.mswdopol.site');
            URL::forceScheme('https');
        }
    }
}
