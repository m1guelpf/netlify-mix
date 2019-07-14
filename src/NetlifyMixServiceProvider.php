<?php

namespace M1guelpf\NetlifyMix;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use M1guelpf\NetlifyMix\Http\Controllers\NetlifyController;
use M1guelpf\NetlifyMix\Http\Middleware\VerifyNetlifyRequest;

class NetlifyMixServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('netlify-mix.php'),
        ]);

        if (! $this->app->routesAreCached()) {
            Route::post('_netlify-mix/webhook', NetlifyController::class)->middleware(VerifyNetlifyRequest::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'netlify-mix');

        // Register the main class to use with the facade
        $this->app->singleton('netlify-mix', function () {
            return new NetlifyMix;
        });
    }
}
