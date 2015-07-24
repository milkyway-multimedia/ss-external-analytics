<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;
/**
 * Milkyway Multimedia
 * Ecommerce.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\DriverAttribute;

use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;

class Ecommerce implements DriverAttribute {
	public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel) {
		$args = ['ecommerce'];

		if($settings = $driver->setting($id, 'EcommerceSettings', null, ['objects' => [$driver]])) {
			$args[] = $settings;
		}

		singleton('ea')->configure('GA.configuration.' . $id . '.attributes.require', [
			$args,
		]);
	}

	public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model) {
		$events = singleton('ea')->unqueue('ecommerce', $id);

		foreach ($events as $type => $options) {
			$event = $this->createEvent($options, $type);

			if(!count($event)) continue;

			if($type == 'setAction') {
				$eventType = $event['eventAction'];
				unset($event['eventAction']);

				singleton('ea')->configure('GA.configuration.' . $id . '.attributes.ec:' . $type, [
					[
						$eventType,
					    $event
					],
				]);
			}
			else {
				singleton('ea')->configure('GA.configuration.' . $id . '.attributes.ec:' . $type, [
					[
						$event
					],
				]);
			}
		}
	}

	protected function createEvent($params = [], $type = 'addImpression')
	{
		$settings = ($type == 'setAction') ? ['eventAction' => 'click'] : [];

		if (is_array($params))
			$settings = array_merge($params, $settings);

		return $settings;
	}
}