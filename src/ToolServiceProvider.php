<?php

namespace Statikbe\NovaMailEditor;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Statikbe\NovaMailEditor\Http\Middleware\Authorize;
use Statikbe\NovaTranslationManager\TranslationManager;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/nova-mail-editor.php' => config_path('nova-mail-editor.php'),
        ], ['nova-mail-editor', 'config']);

        $supportedLocales = config('nova-mail-editor.supported_locales',
            config('app.supported_locales',['en'])
        );

        MailEditor::setLocales($supportedLocales);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova-mail-editor');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });
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

        Route::middleware(['nova', Authorize::class])
                ->prefix('nova-vendor/nova-mail-editor')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nova-mail-editor.php', 'nova-mail-editor'
        );
    }
}
