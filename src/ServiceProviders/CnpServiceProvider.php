<?php
namespace DemocracyApps\CNP\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class CnpServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() 
    {
        $this->app['cnp'] = $this->app->share(function ($app) {
            return new \DemocracyApps\CNP\Utility\Cnp($this->app);
        });
    }
}
