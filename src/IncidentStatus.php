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
 * A class containing our incident status constants.
 *
 * @author Nikki <nospam@nikkii.us>
 */
class IncidentStatus {
	/**
	 * This status is used for a scheduled status.
	 */
	const SCHEDULED = 0;

	/**
	 * You have reports of a problem and you're currently looking into them.
	 */
	const INVESTIGATING = 1;

	/**
	 * You've found the issue and you're working on a fix.
	 */
	const IDENTIFIED = 2;

	/**
	 * You've since deployed a fix and you're currently watching the situation.
	 */
	const WATCHING = 3;

	/**
	 * The fix has worked, you're happy to close the incident.
	 */
	const FIXED = 4;
}