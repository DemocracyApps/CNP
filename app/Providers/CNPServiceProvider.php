<?php namespace DemocracyApps\CNP\Providers;

use DemocracyApps\CNP\Utility\CnpConfiguration;
use DemocracyApps\CNP\Services\JsonProcessor;
use Illuminate\Support\ServiceProvider;

class CNPServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		return $this->app->singleton('cnp', function () {
			$jp = new JsonProcessor();
			return new CnpConfiguration($jp);
		});
	}

}
