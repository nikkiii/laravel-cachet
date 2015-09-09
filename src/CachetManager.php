<?php namespace Nikkiii\Cachet;

/*
 * This file is part of Laravel Cachet.
 *
 * (c) Nikki <nospam@nikkii.us>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GrahamCampbell\Manager\AbstractManager;

/**
 * This is the cachet manager class.
 *
 * @author Nikki <nospam@nikkii.us>
 */
class CachetManager extends AbstractManager {

	/**
	 * Create the connection instance.
	 *
	 * @param array $config
	 *
	 * @return mixed
	 */
	protected function createConnection(array $config) {
		return new CachetConnection([
			'base_uri' => array_get($config, 'url'),
			'token' => array_get($config, 'token')
		]);
	}

	/**
	 * Get the configuration name.
	 *
	 * @return string
	 */
	protected function getConfigName() {
		return 'cachet';
	}
}