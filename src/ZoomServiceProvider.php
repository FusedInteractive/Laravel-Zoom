<?php

namespace Fused\Zoom;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ZoomServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('zoom', function ($app) {
            $config = $app['config']->get('services.zoom');
            return new Zoom($config);
        });

        $this->app->singleton('command.zoom.meetings_table', function ($app) {
            return new ZoomMeetingsTableCommand;
        });

        $this->app->singleton('command.zoom.users_table', function ($app) {
            return new ZoomUsersTableCommand;
        });

        $this->app->alias('zoom', 'Fused\Zoom\Zoom');

        $this->commands('command.zoom.meetings_table');
        $this->commands('command.zoom.users_table');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['zoom', 'Fused\Zoom\Zoom'];
    }
}
