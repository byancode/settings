<?php

namespace Byancode\Settings;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class Provider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/migrations/' => base_path('/database/migrations')
        ]);

        $this->app->singleton('settings', function () {
            return new Settings();
        });

        $this->app->settings->sync();

        Blade::directive('setting', function ($expression) {
            return "<?php echo setting($expression); ?>";
        });
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        # code ...
    }
}
