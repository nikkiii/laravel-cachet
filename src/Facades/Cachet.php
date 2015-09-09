<?php namespace Nikkiii\Cachet\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the cachet facade class.
 *
 * @author Nikki <nospam@nikkii.us>
 */
class Cachet extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'cachet';
	}

}