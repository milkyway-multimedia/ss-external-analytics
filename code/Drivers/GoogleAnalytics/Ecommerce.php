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
	protected static $send_ecommerce = false;

	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
		$script = '';

		if($controller) {
			$controller->extend('updateExternalAnalyticsEcommerceParams', $params, $driver, $id);
			$controller->extend('updateGoogleAnalyticsEcommerceParams', $params, $driver, $id);
		}

		if(isset($params['ecommerce']) && $params['ecommerce'])
			$script = $id . "('require', 'ec');\n";

		$output = [];
		$orders = isset($params['ecommerce']) ? (array)$params['ecommerce'] : [];
		$events = array_merge((array)singleton('ea')->unqueue('ecommerce'), $orders);

		foreach ($events as $type => $options) {
			if($event = $this->createEvent($options, $type)) {
				if($type == 'setAction') {
					$eventType = $event['eventAction'];
					unset($event['eventAction']);
					$event = json_encode($event);
					$output[] = $id . "('ec:$type', $eventType, $event);";
				}
				else {
					$event = json_encode($event);
					$output[] = $id . "('ec:$type', $event);";
				}
			}
		}

		if($controller) {
			$controller->extend('onExternalAnalyticsEcommerce', $output, $driver, $id, $params, $script);
			$controller->extend('onGoogleAnalyticsEcommerce', $output, $driver, $id, $params, $script);
		}

		if(count($output)) {
			static::$send_ecommerce = true;
			$script .= implode("\n", $output);
		}

		if(static::$send_ecommerce) {
			$script .= $id . "('ecommerce:send');\n";
		}

		return $script;
	}

	public static function set_send_ecommerce($flag = true) {
		static::$send_ecommerce = $flag;
	}

	protected function createEvent($params = [], $type = 'addImpression')
	{
		$settings = ($type == 'setAction') ? ['eventAction' => 'click'] : [];

		if (is_array($params))
			$settings = array_merge($params, $settings);

		return $settings;
	}
} 