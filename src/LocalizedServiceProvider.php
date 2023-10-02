<?php

namespace ALajusticia\Localized;

use ALajusticia\Localized\Http\Middleware\Localize;
use ALajusticia\Localized\Http\Middleware\PrefixRoutes;
use ALajusticia\Localized\Macros\BlueprintMacros;
use ALajusticia\Localized\Macros\RouteMacros;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class LocalizedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge default config
        $this->mergeConfigFrom(
            __DIR__.'/../config/localized.php', 'localized'
        );

        app()->singleton(Localized::class, function (Application $app) {
            return new Localized();
        });

        // Register our middleware
        app('router')->aliasMiddleware('localize', Localize::class);
        app('router')->aliasMiddleware('prefixRoutes', PrefixRoutes::class);

        // Set the "locale" route parameter default value to the locale configured in the app.php config file
        URL::defaults(['locale' => Config::get('app.locale')]);

        // Set the global pattern for the "locale" route parameter
        Route::pattern('locale', implode('|', Localized::availableLocales()));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Allow publishing config
        $this->publishes([
            __DIR__.'/../config/localized.php' => config_path('localized.php')
        ], 'config');

        // Register macros
        Blueprint::mixin(new BlueprintMacros);
        Route::mixin(new RouteMacros);

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'localized');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'localized');
    }
}
