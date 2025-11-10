<?php

namespace RiseTechApps\ViewSuite;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

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

        $this->registerPagesExceptions();
    }

    public function registerPagesExceptions(): void
    {
        $this->app->extend(ExceptionHandler::class, function ($handler, $app) {

            return new class($handler) implements ExceptionHandler {
                protected $handler;

                public function __construct(ExceptionHandler $handler)
                {
                    $this->handler = $handler;
                }

                public function report(Throwable $e)
                {
                    return $this->handler->report($e);
                }

                public function shouldReport(Throwable $e)
                {
                    return $this->handler->shouldReport($e);
                }

                public function render($request, Throwable $e)
                {
                    if ($e instanceof HttpExceptionInterface) {
                        $status = $e->getStatusCode();

                        if ($status === 401) {
                            return response()->view('view-suite::errors.401', [], 401);
                        }

                        if ($status === 402) {
                            return response()->view('view-suite::errors.402', [], 402);
                        }

                        if ($status === 403) {
                            return response()->view('view-suite::errors.403', [], 403);
                        }

                        if ($status === 404) {
                            return response()->view('view-suite::errors.404', [], 404);
                        }

                        if ($status === 418) {
                            return response()->view('view-suite::errors.418', [], 418);
                        }

                        if ($status === 419) {
                            return response()->view('view-suite::errors.419', [], 419);
                        }

                        if ($status === 429) {
                            return response()->view('view-suite::errors.429', [], 429);
                        }

                        if ($status === 500) {
                            return response()->view('view-suite::errors.500', [], 500);
                        }

                        if ($status === 502) {
                            return response()->view('view-suite::errors.502', [], 502);
                        }

                        if ($status === 503) {
                            return response()->view('view-suite::errors.503', [], 503);
                        }
                    }

                    return $this->handler->render($request, $e);
                }

                public function renderForConsole($output, Throwable $e)
                {
                    return $this->handler->renderForConsole($output, $e);
                }
            };
        });
    }

}
