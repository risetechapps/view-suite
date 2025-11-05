<?php

namespace RiseTechApps\ViewSuite;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewSuiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {

        $basePath = __DIR__ . '/../resources/views';

        $this->loadViewsFrom($basePath, 'view-suite');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'view-suite');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('view-suite.php'),
            ], 'config');

            $this->publishes([
                $basePath => resource_path('views/vendor/view-suite'),
            ], 'views');
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

        $this->app->extend('view.finder', function ($finder, $app) {
            $paths = $finder->getPaths();

            $extensions = (function () {
                return $this->extensions;
            })->call($finder);

            $hints = (function () {
                return $this->hints;
            })->call($finder);

            $themedFinder = new ThemedViewFinder($app['files'], $paths, $extensions);

            foreach ($hints as $namespace => $namespacePaths) {
                $themedFinder->addNamespace($namespace, $namespacePaths);
            }

            return $themedFinder;
        });
    }

}
