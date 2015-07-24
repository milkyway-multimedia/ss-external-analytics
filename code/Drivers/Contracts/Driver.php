<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Contracts;
/**
 * Milkyway Multimedia
 * Driver.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

interface Driver {
	/** @return string */
	public function title($id);

	/** @return array */
	public function db($id);

	/** @return array */
	public function db_to_environment_mapping($id);

	/** @return mixed */
	public function configuration($id);

	/** @return mixed */
	public function setting($id, $setting, $default = null, $cache = true);
}