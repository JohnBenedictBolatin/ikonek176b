<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        if (!$this->app->environment('local')) {
            URL::forceScheme('https');
            $appUrl = config('app.url');
            if ($appUrl && str_starts_with((string) $appUrl, 'http://')) {
                $host = parse_url($appUrl, PHP_URL_HOST);
                $port = parse_url($appUrl, PHP_URL_PORT);
                $path = parse_url($appUrl, PHP_URL_PATH) ?? '';
                URL::forceRootUrl('https://' . $host . ($port ? ":{$port}" : '') . rtrim($path, '/'));
            }
        }
        Vite::prefetch(concurrency: 3);
        View::share('birthdate', date('B'));
        View::share('birthdate', date('B'));
    }
}
