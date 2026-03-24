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
        // Avoid localhost vs 127.0.0.1 host switching during local development which can break sessions/CSRF.
        if (! app()->runningInConsole() && app()->environment('local')) {
            URL::forceRootUrl(request()->root());
        }
    }
}
