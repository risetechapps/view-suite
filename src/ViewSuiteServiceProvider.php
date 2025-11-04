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

        $themes = config('view-suite.theme');

        View::composer('*', function ($view) use ($basePath, $themes) {
            $viewName = $view->name();

            // 🔹 Erros
            if (str_starts_with($viewName, 'view-suite::errors.')) {
                $this->resolveThemedView($view, $basePath, 'errors', $themes['error']);
            }

            // 🔹 E-mails
            if (str_starts_with($viewName, 'view-suite::emails.')) {
                $this->resolveThemedView($view, $basePath, 'emails', $themes['email']);
            }
        });

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
    }

    /**
     * Resolve dinamicamente o caminho de uma view tematizada.
     */
    protected function resolveThemedView($view, string $basePath, string $folder, string $theme): void
    {
        $name = str_replace("view-suite::{$folder}.", '', $view->name());

        // O Laravel tenta "emails.index" → resolvemos para o tema configurado
        $customPath = "{$basePath}/{$folder}/{$theme}/{$name}.blade.php";
        $defaultPath = "{$basePath}/{$folder}/default/{$name}.blade.php";
        if($folder === 'email'){
            dd($customPath, File::exists($customPath));
        }
        // 🔹 Ajuste: Se o arquivo existe no tema atual, forçamos o caminho
        if (File::exists($customPath)) {

            $view->setPath($customPath);
        } elseif (File::exists($defaultPath)) {
            $view->setPath($defaultPath);
        } else {
            // fallback final — deixa o Laravel cuidar se nenhuma view foi encontrada
            $view->setPath("{$basePath}/{$folder}/default/{$name}.blade.php");
        }
    }

}
