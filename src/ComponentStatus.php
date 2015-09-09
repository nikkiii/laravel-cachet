<?php namespace Nikkiii\Cachet;

/*
 * This file is part of Laravel Cachet.
 *
 * (c) Nikki <nospam@nikkii.us>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A class containing our component status constants.
 *
 * @author Nikki <nospam@nikkii.us>
 */
class ComponentStatus {
	/*
	 * The component is working.
	 */
	const OPERATIONAL = 1;

	/**
	 * The component is experiencing some slowness.
	 */
	const PERFORMANCE_ISSUES = 2;

	/**
	 * The component may not be working for everybody. This could be a geographical issue for example.
	 */
	const PARTIAL_OUTAGE = 3;

	/**
	 * The component is not working for anybody.
	 */
	const MAJOR_OUTAGE = 4;
}