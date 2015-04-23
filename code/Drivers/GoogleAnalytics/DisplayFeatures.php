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

class DisplayFeatures implements ScriptAttribute {
	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
		$script = '';

		$params = array_merge([
			'display_features' => singleton('env')->get('GoogleAnalytics.ga_enable_display_features', true),
		], $params);

		if($controller) {
			$controller->extend('updateExternalAnalyticsDisplayFeaturesParams', $params, $driver, $id);
			$controller->extend('updateGoogleAnalyticsDisplayFeaturesParams', $params, $driver, $id);
		}

		if(isset($params['display_features']) && $params['display_features'])
			$script = $id . "('require', 'displayfeatures');\n";

		if($controller) {
			$controller->extend('onExternalAnalyticsDisplayFeatures', $output, $driver, $id, $params, $script);
			$controller->extend('onGoogleAnalyticsDisplayFeatures', $output, $driver, $id, $params, $script);
		}

		return $script;
	}
} 