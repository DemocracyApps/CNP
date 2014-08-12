<?php
namespace DemocracyApps\CNP\Providers;

use Illuminate\Support\ServiceProvider;

class CnpProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() 
    {
        $this->app['cnp'] = $this->app->share(function ($app) {
            return new Cnp($this->app);
        });
    }
}
