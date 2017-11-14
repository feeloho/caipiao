<?php namespace App\Providers\HttpClient;

use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider {

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
		$this->app->bind('HttpClient', function() {
			return new HttpClient;
		});
	}

}
