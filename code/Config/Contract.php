<?php
/**
 * Milkyway Multimedia
 * Contract.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\Config;

interface Contract {
	/** @return string */
	public function i18n_title();

	/** @return string */
	public function prefix();

	/** @return array */
	public function db();

	/** @return array */
	public function map();

	/** @return string */
	public function findClientId();

	/** @return string */
	public function javascript($controller, $params = []);
}