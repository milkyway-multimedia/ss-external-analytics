<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;
/**
 * Milkyway Multimedia
 * Ecommerce.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\ScriptAttribute;
use ViewableData;

class Ecommerce implements ScriptAttribute {
	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
		$script = '';

		if(isset($params['ecommerce']) && $params['ecommerce'])
			$script = $id . "('require', 'ec');\n";

		if($controller) {
			$controller->extend('onExternalAnalyticsEcommerce', $script, $driver, $id, $params);
		}

		return '';
	}
} 