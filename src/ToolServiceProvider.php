<?php

namespace SimonHamp\LaravelNovaCsvImport;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SimonHamp\LaravelNovaCsvImport\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {

        });

        $this->publishes([
            __DIR__.'/config.php' => config_path('nova-csv-importer.php')
        ], 'nova-csv-import');
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authorize::class], 'csv-import')
            ->namespace(__NAMESPACE__ . '\\Http\\Controllers')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->namespace(__NAMESPACE__ . '\\Http\\Controllers')
            ->prefix('nova-vendor/laravel-nova-csv-import')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'nova-csv-importer');
    }
}
