<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;
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

		if($controller) {
			$controller->extend('updateExternalAnalyticsEcommerceParams', $params, $driver, $id);
		}

		$orders = (array)singleton('ea')->unqueue('ecommerce');

		if(count($orders)) {

			$configParams = [
				'objects' => [$controller, $this]
			];
			$orderDefaults = [
					'driver_id' => $id,
					'store_id' => $driver->setting($id, 'StoreId', null, $configParams),
					'store_name' => $driver->setting($id, 'StoreName', null, $configParams),
			];

			foreach ($orders as $type => $options) {
				$order = array_merge($orderDefaults, $options);

				if (!isset($order['id'])) {
					$order['id'] = $type;

					singleton('Milkyway\SS\ExternalAnalytics\Listeners\RecordViaEcommerce360')
						->ecommerce(null, 'ecommerce', $order);
				}
			}
		}

		if($controller) {
			$controller->extend('onExternalAnalyticsEcommerce', $output, $driver, $id, $params, $script);
		}

		return $script;
	}

	protected function createEvent($params = [], $type = 'addImpression')
	{
		$settings = ($type == 'setAction') ? ['eventAction' => 'click'] : [];

		if (is_array($params))
			$settings = array_merge($params, $settings);

		return $settings;
	}
} 