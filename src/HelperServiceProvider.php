<?php

namespace Mainul\CustomHelperFunctions;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/helper-functions.php',
            'helper-functions'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/config/helper-functions.php' => config_path('helper-functions.php'),
        ], 'helper-functions-config');

        // Load routes if enabled in config
        if (config('helper-functions.routes.web.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        if (config('helper-functions.routes.api.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }

        // Publish routes files for customization
        $this->publishes([
            __DIR__ . '/routes/web.php' => base_path('routes/helper-functions-web.php'),
            __DIR__ . '/routes/api.php' => base_path('routes/helper-functions-api.php'),
        ], 'helper-functions-routes');
    }
}
