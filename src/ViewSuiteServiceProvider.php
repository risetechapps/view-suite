<?php

namespace RiseTechApps\ViewSuite;

use Illuminate\Support\ServiceProvider;

class ViewSuiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('view-suite.php'),
            ], 'config');

        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'view-suite');

        // Register the main class to use with the facade
        $this->app->singleton('view-suite', function () {
            return new ViewSuite;
        });
    }
}
