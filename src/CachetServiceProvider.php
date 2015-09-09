<?php namespace Nikkiii\Cachet;

/*
 * This file is part of Laravel Cachet.
 *
 * (c) Nikki <nospam@nikkii.us>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\ServiceProvider;

/**
 * This is the cachet service provider class.
 *
 * @author Nikki <nospam@nikkii.us>
 */
class CachetServiceProvider extends ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot() {
		$this->setupConfig();
	}

	/**
	 * Setup the config.
	 *
	 * @return void
	 */
	protected function setupConfig() {
		$source = realpath(__DIR__ . '/../config/cachet.php');

		if (class_exists('Illuminate\Foundation\Application', false)) {
			$this->publishes([$source => config_path('cachet.php')]);
		}

		$this->mergeConfigFrom($source, 'cachet');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerManager($this->app);
		$this->registerBindings($this->app);
	}

	/**
	 * Register the manager class.
	 *
	 * @param \Illuminate\Contracts\Foundation\Application $app
	 *
	 * @return void
	 */
	protected function registerManager(Application $app) {
		$app->singleton('cachet', function ($app) {
			$config = $app['config'];
			return new CachetManager($config);
		});
		$app->alias('cachet', CachetManager::class);
	}

	/**
	 * Register the bindings.
	 *
	 * @param \Illuminate\Contracts\Foundation\Application $app
	 *
	 * @return void
	 */
	protected function registerBindings(Application $app) {
		$app->bind('cachet.connection', function ($app) {
			$manager = $app['cachet'];
			return $manager->connection();
		});
		$app->alias('cachet.connection', DigitalOceanV2::class);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides() {
		return [
			'cachet',
			'cachet.connection',
		];
	}
}